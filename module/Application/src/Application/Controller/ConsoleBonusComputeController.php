<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CarsService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PoisService;
use SharengoCore\Service\EditTripsService;
use SharengoCore\Service\BonusService;
use SharengoCore\Service\ZonesService;
use SharengoCore\Service\EventsService;
use SharengoCore\Service\EmailService;
use SharengoCore\Service\CarsBonusService;
use SharengoCore\Service\CarsBonusHistoryService;
use SharengoCore\Service\ServerScriptsService;
use SharengoCore\Service\AccountedTripsService;
use SharengoCore\Service\FleetService;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\ZoneBonus;
use SharengoCore\Entity\CarsBonus;
use SharengoCore\Entity\CustomersPoints;
use SharengoCore\Entity\Fleet;
use SharengoCore\Entity\Trips;
use SharengoCore\Entity\CarsBonusHistory;
use SharengoCore\Service\SimpleLoggerService as Logger;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use SharengoCore\Utils\Interval;

class ConsoleBonusComputeController extends AbstractActionController {

    /**
     * @var CustomersService
     */
    private $customerService;

    /**
     * @var AccountedTripsService
     */
    private $accountedTripsService;

    /**
     * @var ServerScriptService
     */
    private $serverScriptService;

    /**
     * @var CarsService
     */
    private $carsService;

    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @var EditTripsService
     */
    private $editTripService;

    /**
     * @var BonusService
     */
    private $bonusService;

    /**
     * @var ZonesService
     */
    private $zonesService;

    /**
     * @var PoisService
     */
    private $poisService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var EventsService
     */
    private $eventsService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $pointConfig;

    /**
     * @var
     */
    private $customerPointForm;

    /**
     * @var boolean
     */
    private $avoidEmails;
    
    /**
     * @var FleetService
     */
    private $fleetService;
    
    /**
     * @var array
     */
    private $positionConfig;
    
    /**
     * @var CarsBonusService
     */
    private $carsBonusService;
    
    /**
     * @var CarsBonusHistoryService
     */
    private $carsBonusHistoryService;


    /**
     * @param CustomersService $customerService
     * @param ServerScriptsService $serverScriptServic
     * @param AccountedTripsService $accountedTripsService
     * @param TripsService $tripsService
     * @param TripPaymentsService $tripPaymentsService
     * @param EditTripsService $editTripService
     * @param BonusService $bonusService
     * @param ZonesService $zonesService
     * @param EventsService $eventsService
     * @param Logger $logger
     * @param array $config
     * @param array $pointConfig
     * @param Form $customerPointForm
     * @param FleetService $fleetService
     * @param array $positionConfig
     * @param CarsBonusService $carsBonusService
     * @param CarsBonusHistoryService $carsBonusHistoryService
     */
    public function __construct(
    CustomersService $customerService, ServerScriptsService $serverScriptService, AccountedTripsService $accountedTripsService, CarsService $carsService, TripsService $tripsService, TripPaymentsService $tripPaymentsService, EditTripsService $editTripService, BonusService $bonusService, ZonesService $zonesService, EmailService $emailService, PoisService $poisService, EventsService $eventsService, Logger $logger, $config, $pointConfig, Form $customerPointForm, FleetService $fleetService, $positionConfig, CarsBonusService $carsBonusService, CarsBonusHistoryService $carsBonusHistoryService
    ) {
        $this->customerService = $customerService;
        $this->serverScriptService = $serverScriptService;
        $this->accountedTripsService = $accountedTripsService;
        $this->carsService = $carsService;
        $this->tripsService = $tripsService;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->editTripService = $editTripService;
        $this->bonusService = $bonusService;
        $this->poisService = $poisService;
        $this->emailService = $emailService;
        $this->zonesService = $zonesService;
        $this->eventsService = $eventsService;
        $this->logger = $logger;
        $this->config = $config;
        $this->customerPointForm = $customerPointForm;
        $this->pointConfig = $pointConfig['point'];
        $this->fleetService = $fleetService;
        $this->positionConfig = $positionConfig;
        $this->carsBonusService = $carsBonusService;
        $this->carsBonusHistoryService = $carsBonusHistoryService;
    }

    public function bonusComputeAction() {
        $this->prepareLogger();

        $this->logger->log("\nStarted computing for bonuses trips\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $this->zoneBonusCompute(); // TODO: de-comment in production
        $this->zoneExtraFareCompute();
    }

    public function zoneBonusCompute() {
        $tripsToBeComputed = $this->tripsService->getTripsForBonusComputation();

        $this->logger->log("-------- Compute Zone Bonuses\n");
        $this->logger->log("Trips to compute: " . count($tripsToBeComputed) . "\n\n");

        foreach ($tripsToBeComputed as $trip) {

            if (!$trip instanceof Trips) {
                continue;
            }

            // Put to true $bonusComputed in trips
            $this->editTripService->doEditTripBonusComputed($trip, true);

            if ($trip->getCustomer()->getGoldList() || $trip->getCustomer()->getMaintainer()) {
                continue;
            }

            // Verify if there are zone bonuses in that fleet
            $zonesBonus = $this->zonesService->getListZonesBonusByFleet($trip->getFleet());
            if (count($zonesBonus) == 0) {
                continue;
            }

            // Verify if customer reached max amount in zone bonuses passed and return a list of those available
            $residuals = $this->findBonusUsable($trip, $zonesBonus);
            if (count($residuals) == 0) {
                continue;
            }

            // Read and process trip events to find stops for parking, contolling if they obtain zone bonuses
            $this->verifyBonus($trip, $zonesBonus, $residuals);

            // Assign zone bonuses to customer
            foreach ($residuals as $zone => $attribs) {
                if ($attribs["adding"] > 0) {
                    $this->assigneBonus($trip, $attribs["adding"], $zone, $attribs["duration"], "Parking bonus " . $attribs["name"]);
                }
            }
        }
    }

    private function zoneExtraFareCompute() {
        $tripsToBeComputed = $this->tripsService->getTripsForExtraFareComputation();
        $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;zoneExtraFareCompute;start;" . count($tripsToBeComputed) . "\n");
        $zonesBonus = $this->zonesService->getListZonesBonusForExtraFare();
        $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;zoneExtraFareCompute;zonesBonus;" . count($zonesBonus) . "\n");

        foreach ($tripsToBeComputed as $trip) {     // loop through trips
            $extraFareDescription = "";
            $extraFareAmount = $this->zoneExtraFareGetAmount($trip, $zonesBonus, $extraFareDescription);
            $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;zoneExtraFareCompute;amount;" . $trip->getId() . ";" . $extraFareAmount . "\n");
            $this->zoneExtraFareAddAmount($trip, $extraFareDescription, $extraFareAmount);
        }

        $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;zoneExtraFareCompute;end;\n");
    }

    private function verifyBonus(Trips $trip, array $zonesBonusByFleet, array &$residuals) {
        $events = $this->eventsService->getEventsByTrip($trip);
        $time_beginning = 0;
        $is_bonus_parking = false;
        $bonus_attribs = 0;

        foreach ($events as $event) {
            // Search stop begin
            if ($event->getEventId() == 3 && $event->getIntval() == 3) { // getLabel()
                $zonesBonus = $this->zonesService->checkPointInBonusZones(
                        $zonesBonusByFleet, $event->getLon(), $event->getLat());

                if (count($zonesBonus) > 0) {
                    foreach ($residuals as $zone => &$attribs) {
                        if ($attribs["adding"] < $attribs["residual"] &&
                                $zone === strtolower($zonesBonus[0]->getBonusType())) {
                            $tb = $event->getEventTime();
                            if (isset($tb)) {
                                $time_beginning = $tb;
                                $bonus_attribs = &$attribs; //By reference
                                $is_bonus_parking = true;
                            }
                            break;
                        }
                    }
                }
            }
            // Search stop end
            else if ($event->getEventId() == 3 && $event->getIntval() == 4) { // getLabel()
                if ($is_bonus_parking) {
                    $is_bonus_parking = false;
                    $te = $event->getEventTime();
                    if (isset($te)) {
                        $time_ending = $te;
                        $minTime = new \DateTime('2016-01-01');

                        $int1 = $time_beginning->getTimestamp() - $minTime->getTimestamp();
                        $int2 = $time_ending->getTimestamp() - $minTime->getTimestamp();

                        if ($int1 > 0 && $int2 > 0 && $int2 > $int1) {
                            $intstop = intval(floor(($int2 - $int1) / 60));
                            if ($intstop >= $bonus_attribs["minMinutes"]) {
                                if ($bonus_attribs["fixedBonus"] > 0)
                                    $intstop = $bonus_attribs["fixedBonus"];
                                $maxBonus = $bonus_attribs["residual"] - $bonus_attribs["adding"];
                                if ($intstop >= $maxBonus) {
                                    $bonus_attribs["adding"] = $bonus_attribs["residual"];
                                } else {
                                    $bonus_attribs["adding"] += $intstop;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function assigneBonus(Trips $trip, $bonus_to_assign, $bonus_type, $duration, $description) {
        $validFrom = $trip->getTimestampBeginning();
        $valFrom = $validFrom->format("Y-m-d");
        $validTo = new \DateTime($valFrom);
        $validTo->add(new \DateInterval('P' . strval($duration) . 'D')); //aggiungo i giorni di durata
        $valTo = $validTo->format("Y-m-d");

        $bonus = $this->bonusService->createBonusForCustomerFromData($trip->getCustomer(), $bonus_to_assign, 'zone-' . $bonus_type, $description, $valTo, $valFrom);

        $this->logger->log(sprintf("%s;INF;assigneBonus;tripId=%s;bonusId=%s;customerId=%s;email=%s;carPlate=%s\n",
            date_create()->format('Y-m-d H:i:s'),
            $trip->getId(),
            $bonus->getId(),
            $trip->getCustomer()->getId(),
            $trip->getCustomer()->getEmail(),
            $trip->getCar()->getPlate()));

    }

    private function findBonusUsable(Trips $trip, array &$zonesBonus) {
        $residuals = array();

        $zonesBonusNoDuplicate = array();
        foreach ($zonesBonus as $zoneBonus) {
            $notFound = true;
            foreach ($zonesBonusNoDuplicate as $zb) {
                if (strtolower($zoneBonus->getBonusType()) === strtolower($zb->getBonusType())) {
                    $notFound = false;
                    break;
                }
            }
            if ($notFound) {
                $zonesBonusNoDuplicate[] = $zoneBonus;
            }
        }

        foreach ($zonesBonusNoDuplicate as $zoneBonus) {
            $bonus_type = strtolower($zoneBonus->getBonusType());
            $customerBonuses = $this->customerService->getBonusesForCustomerIdAndDateInsertionAndType(
                    $trip->getCustomer(), $trip->getTimestampBeginning(), 'zone-' . $bonus_type);

            $zone_bonus_sum = 0;
            foreach ($customerBonuses as $customerBonus) {
                $zone_bonus_sum += $customerBonus->getTotal();
            }

            $total = 30;
            $duration = 60;
            $fixedBonus = 0;
            $minMinutes = 1;
            if (isset($this->config["defaultTotal"])) {
                $total = $this->config["defaultTotal"];
            }
            if (isset($this->config["defaultDuration"])) {
                $duration = $this->config["defaultDuration"];
            }
            foreach ($this->config as $zone => $attribs) {
                if (strtolower($zone) === $bonus_type) {
                    if (isset($attribs["total"])) {
                        $total = $attribs["total"];
                    }
                    if (isset($attribs["duration"])) {
                        $duration = $attribs["duration"];
                    }
                    if (isset($attribs["fixedBonus"])) {
                        $fixedBonus = $attribs["fixedBonus"];
                    }
                    if (isset($attribs["minMinutes"])) {
                        $minMinutes = $attribs["minMinutes"];
                    }
                    break;
                }
            }

            if ($zone_bonus_sum < $total) {
                $residuals[$bonus_type] = array(
                    "residual" => $total - $zone_bonus_sum,
                    "adding" => 0,
                    "duration" => $duration,
                    "name" => $zoneBonus->getBonusType(),
                    "fixedBonus" => $fixedBonus,
                    "minMinutes" => $minMinutes
                );
            }
        }

        //Remove bonus zones not interested
        $zonesBonusInterested = array();
        foreach ($zonesBonus as $zoneBonus) {
            foreach ($residuals as $zone => $attribs) {
                if (strtolower($zoneBonus->getBonusType()) === $zone) {
                    $zonesBonusInterested[] = $zoneBonus;
                    break;
                }
            }
        }
        $zonesBonus = $zonesBonusInterested;

        return $residuals;
    }

    private function prepareLogger() {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);
    }

    private function validateDate($date) {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /*
     * this method verify/calculate if one customer can receive point day
     */
    public function addPointDayAction() {

        $this->prepareLogger();
        $format = "%s;INF;addPointDayAction;strat\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

        $request = $this->getRequest();
        //explain corret format paramDate ->
        //$paramDate="2017-10-05";
        $paramDate = $request->getParam('date');

        $serverScriptDay = new \SharengoCore\Entity\ServerScripts();

        try{

            $this->serverScriptService->writeStartServerScript($serverScriptDay);

            if (!is_null($paramDate)) {
                $format = "%s;INF;addPointDayAction;script with date param\n";
                $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
                if ($this->validateDate($paramDate)) {
                    $format = "%s;INF;addPointDayAction;DateParam= %s\n";
                    $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s'), $paramDate));
                    $date = new \DateTime($paramDate);
                    $arrayDates = $this->createDate($date);
                    $this->scriptAddPointDay($arrayDates, TRUE, $serverScriptDay);
                } else {
                    $format = "%s;INF;addPointDayAction;date param NOT VALID!;DateParam= %s \n";
                    $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s'), $paramDate));
                }
            } else {
                $format = "%s;INF;addPointDayAction;script NO with date param\n";
                $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
                $arrayDates = $this->createDate();
                $this->scriptAddPointDay($arrayDates, FALSE, $serverScriptDay);
                $this->addPointClusterAction();
            }
        } catch (\Exception $e) {
            $this->writeServerScript($this->pointConfig['nameAddPointDay'], $serverScriptDay, $paramDate, $e, "ERROR");
            $format = "%s;ERROR;addPointDayAction;end with Exception consult table server_scripts!\n";
            $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
        }
    }// end addPointDayAction

    private function writeServerScript($scriptName, $serverScript, $paramDate = null, \Exception $e = null, $note = null) {

        //get info script
        $info_scritp = get_included_files();
        //get script path
        $path_script = $info_scritp[0];

        $serverScript->setName($scriptName);
        $serverScript->setFullPath($path_script);
        $serverScript->setParam((!is_null($paramDate) ? json_encode(['date' => $paramDate]) : null));
        $serverScript->setError((!is_null($e) ? $e->getMessage() : null));
        if(!is_null($note))
            $serverScript->setNote($note);

        $this->serverScriptService->writeRow($serverScript);
    }

    private function scriptAddPointDay($arrayDates, $param, $serverScriptDay) {

        //get customer in range date
        $customers = $this->customerService->getCustomersRunYesterday($arrayDates[0], $arrayDates[1]);

        if($param){
            $oldServerScript = $this->serverScriptService->getOldServerScript($arrayDates[1]);

            $this->writeServerScript($this->pointConfig['nameAddPointDay'], $serverScriptDay, $arrayDates[1]);

            //if row number > 0 -> diff customer
            if(count($oldServerScript)>0){
                //if there are more row of 0, i take first element, because i order the resul query.
                //get the last run of script with index 0.
                $dataOldServerScript = json_decode($oldServerScript[0]->getInfoScript());
                $lastCustomer = $dataOldServerScript->lastCustomer;
                //diff customer, calculate new array of customers
                $newCustomers = $this->calculateNewCustomers($customers, $lastCustomer);
                $this->executeScriptAddPointDay($newCustomers, $arrayDates, $serverScriptDay);
            }
        }else{

            $this->writeServerScript($this->pointConfig['nameAddPointDay'], $serverScriptDay);

            $this->executeScriptAddPointDay($customers, $arrayDates, $serverScriptDay);
        }

        //write in customer-points the end scirpt
        $this->serverScriptService->writeEndServerScript($serverScriptDay);

        $format = "%s;INF;addPointDayAction;end\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

    }//scriptAddPointDay

    private function executeScriptAddPointDay($customers, $arrayDates, $serverScriptDay){
        //update field InfoScript in tabel server_scripts before customer procressed
        //set field infoScript with data preExecte
        $this->updateInfoScriptServerScript($serverScriptDay, $customers);

        foreach ($customers as $c){

            $tripsYesterday = $this->tripsService->getTripsByCustomerForAddPointYesterday($c['id'], $arrayDates[0], $arrayDates[1]);

            $minuteTripsYesterday = 0;
            $pointToAdd = 0;

            if (count($tripsYesterday) > 0) {
                foreach ($tripsYesterday as $tripYesterday) {
                    $interval = new Interval($tripYesterday->getTimestampBeginning(), $tripYesterday->getTimestampEnd());
                    $minuteTripsYesterday += $interval->minutes();

                    //remove minutes from table trip_free_fares     ---> return zero or one result
                    $freeMinutesTripFreeFares = $this->accountedTripsService->findFreeMinutesByTripIdFromTripFreeFraes($tripYesterday->getId());
                    if (count($freeMinutesTripFreeFares) > 0)
                        $minuteTripsYesterday -= $freeMinutesTripFreeFares[0]->getMinutes();

                    //remove minutes from table trip_bonuses mapped type in table customers_bonus     ---> return zero, one or more result
                    $freeMinutesTripBonuses = $this->accountedTripsService->findFreeMinutesByTripIdFromTripBonuses($tripYesterday->getId());
                    if (count($freeMinutesTripBonuses) > 0)
                        foreach ($freeMinutesTripBonuses as $item) {
                            if ($item->getBonus()->getType() == "promo" ||
                                    $item->getBonus()->getType() == "zone-POIS" ||
                                    $item->getBonus()->getType() == "zone-carrefour" ||
                                    $item->getBonus()->getType() == "birthday" ||
                                    $item->getBonus()->getType() == "bonus" ||
                                    $item->getBonus()->getType() == "PacchettoPunti"
                            )
                                $minuteTripsYesterday -= $item->getMinutes();
                        }
                }
            }

            //if $minuteTripsYesterday < 0 set $minuteTripsYesterday = 0 because not generate points negative
            if($minuteTripsYesterday < 0)
                $minuteTripsYesterday = 0;

            //check if $minuteTripsYesterday exceeds the point limit to day
            if($minuteTripsYesterday > $this->pointConfig['maxValPointDay']){
                $pointToAdd = $this->pointConfig['maxValPointDay'];
            }else{
                $pointToAdd = $minuteTripsYesterday;
            }

            //check if customer have alrady line, for this month, in customers_points
            $customerPoints = $this->checkCustomerIfAlreadyAddPointsThisMonth($c['id'], $arrayDates[2], $arrayDates[3], $arrayDates[1]);
            //add or update line point in customers_points
            if (count($customerPoints) > 0) {
                $this->updateCustomersPoints($pointToAdd, $customerPoints[0], $c['id']);
            } else {
                $this->addCustomersPoints($pointToAdd, $c['id'], $this->pointConfig['descriptionScriptAddPointDay'], $this->pointConfig['typeDrive']);
            }

            //update the field InfoScript in tabel server_scripts after customer procressed
            //set field infoScript with data customers precessed
            $this->updateInfoScriptServerScript($serverScriptDay, $customers, $c['id']);

            $this->customerService->clearEntityManager();

        }//end foreach customers
    }//end executeScriptAddPointDay

    private function updateInfoScriptServerScript($serverScript, $customers, $id = null){
        if(!is_null($id)){
            $numbCustomerProcessed = json_decode($serverScript->getInfoScript());
            $numbCustomerProcessedMoreOne = $numbCustomerProcessed->numbCustomerProcessed + 1;
            $serverScript->setInfoScript(json_encode([
                                                'totaNumbCustomerProcess' => count($customers),
                                                'numbCustomerProcessed' => $numbCustomerProcessedMoreOne,
                                                'lastCustomer' => $id
                                            ])
                                        );
        }else{
             $serverScript->setInfoScript(json_encode([
                                            'totaNumbCustomerProcess' => count($customers),
                                            'numbCustomerProcessed' => 0,
                                            'lastCustomer' => -1
                                            ])
                                        );
        }

        $this->serverScriptService->writeRow($serverScript);
    }

    private function calculateNewCustomers($customers, $lastCustomerProcessed) {
        //create a new array's customers
        $newCustomers = array();
        foreach ($customers as $customer){
            if($customer['id'] > $lastCustomerProcessed)
                $newCustomers [] = $customer;
        }
        return $newCustomers;
    }

    private function createDate(\DateTime $date = null) {

        if (is_null($date)) {
            $dateYesterdayStart = new \DateTime();
            $dateYesterdayStart = $dateYesterdayStart->modify("-1 day");
            $dateYesterdayStart = $dateYesterdayStart->format("Y-m-d 00:00:00");

            $dateTodayStart = new \DateTime();
            $dateTodayStart = $dateTodayStart->format("Y-m-d 00:00:00");


            $dateCurrentMonthStart = new \DateTime('first day of this month');
            $dateCurrentMonthStart = $dateCurrentMonthStart->format("Y-m-d 00:00:00");

            $dateNextMonthStart = new \DateTime('first day of next month');
            $dateNextMonthStart = $dateNextMonthStart->format("Y-m-d 00:00:00");

            $dates[0] = $dateYesterdayStart;
            $dates[1] = $dateTodayStart;
            $dates[2] = $dateCurrentMonthStart;
            $dates[3] = $dateNextMonthStart;

            return $dates;

        } else {
            $dateStart = $date->format("Y-m-d 00:00:00");

            $dateMonthStart = $date->format("Y-m-d 00:00:00");
            $dateMonthStart = date("Y-m-01 00:00:00", strtotime($dateMonthStart));

            $date1 = new \DateTime($date->format("Y-m-d 00:00:00"));
            $dateLastStart = $date1->modify("-1 day");
            $dateLastStart = $dateLastStart->format("Y-m-d 00:00:00");

            $date2 = new \DateTime($date->format("Y-m-d 00:00:00"));
            $dateNextMonthStart = ($date2->modify("+1 month")->format("Y-m-d 00:00:00"));
            $dateNextMonthStart = date("Y-m-01 00:00:00", strtotime($dateNextMonthStart));

            $dates[0] = $dateLastStart;
            $dates[1] = $dateStart;
            $dates[2] = $dateMonthStart;
            $dates[3] = $dateNextMonthStart;

            return $dates;
        }
    }

    /*
     * create obj customer and add to db
     * for clas customerPoint Form, FormFactory e Fieldset
     * they are copied to the same admin class
     */

    private function addCustomersPoints($numeberAddPoint, $customerId, $nameScript, $type) {

        $point = new \SharengoCore\Entity\CustomersPoints();

        $date = new \DateTime();
        $date2 = new \DateTime();
        $dateAdd10year = $date2->modify('+10 years');

        $point->setTotal($numeberAddPoint);
        $point->setDescription("add row to script: " . $nameScript);
        $point->setValidFrom($date);
        $point->setValidTo($dateAdd10year);
        $point->setInsertTs($date);
        $point->setUpdateTs($date);
        $point->setResidual(0);
        $point->setType($type);

        $this->customerService->setPointField($point, $customerId, $type);

        $format = "%s;INF;addCustomersPoints;Customer_id= %d;Add= %d;Script name= %s\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s'), $customerId, $numeberAddPoint, $nameScript));

    }

    private function updateCustomersPoints($numeberAddPoint, CustomersPoints $customerPoint, $customerId) {

        $customerPoint->setTotal($customerPoint->getTotal() + $numeberAddPoint);
        $customerPoint->setUpdateTs(new \DateTime());
        $this->customerService->updateCustomerPointRow($customerPoint);

        $format = "%s;INF;updateCustomersPoints;Customer_id= %d;Add= %d;PrevPoints= %d\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s'), $customerId, $numeberAddPoint, $customerPoint->getTotal()));

    }

    /*
     * this method verify if one customer can receive this bonus
     */
    public function addPointClusterAction() {

        $this->prepareLogger();
        $this->logger->log("\n\n-----------------------------------------------\n\n");
        $format = "%s;INF;addPointClusterAction;strat\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

        $serverScriptCluster = new \SharengoCore\Entity\ServerScripts();

        try{

            $this->serverScriptService->writeStartServerScript($serverScriptCluster);

            $request = $this->getRequest();
            $this->avoidEmails = $request->getParam('no-emails') || $request->getParam('e');

            $this->writeServerScript($this->pointConfig['nameAddPointCluster'], $serverScriptCluster);

            $dateTodayStart = new \DateTime();
            $dateTodayStart = $dateTodayStart->format("Y-m-d 00:00:00");

            $dateYesterdayStart = new \DateTime();
            $dateYesterdayStart = $dateYesterdayStart->modify('-1 day');

            $dateStartCurrentMonth = new \DateTime($dateYesterdayStart->format("Y-m-d 00:00:00"));
            $dateStartCurrentMonth = $dateStartCurrentMonth->modify('first day of this month');
            $dateStartCurrentMonth = $dateStartCurrentMonth->format("Y-m-d 00:00:00");

            $dateStartLastMonth = new \DateTime($dateStartCurrentMonth);
            $dateStartLastMonth = $dateStartLastMonth->modify("-1 month");
            $dateStartLastMonth = $dateStartLastMonth->format("Y-m-d 00:00:00");

            $customers = $this->customerService->getCustomersRunThisMonth($dateTodayStart, $dateStartCurrentMonth);

            foreach ($customers as $c) {
                if ($this->checkCustomerAlreadyAddPointsCluster($c['id'])) {
                    $earnedPointsThisMonth = $this->customerService->getTripsByCustomerForAddPointClusterLastMonth($c['id'], $dateTodayStart, $dateStartCurrentMonth);
                    if (count($earnedPointsThisMonth) > 0) {
                        if ($earnedPointsThisMonth[0]->getTotal() >= $this->pointConfig['newCheckPointCluster']) {
                            $earnedPointsLastMonth = $this->customerService->getTripsByCustomerForAddPointClusterTwotMonthAgo($c['id'], $dateStartLastMonth, $dateStartCurrentMonth);
                            if (count($earnedPointsLastMonth) > 0) {
                                if ($earnedPointsLastMonth[0]->getTotal() < $this->pointConfig['oldCheckPointCluster']) {
                                    //add 1000 points for pass cluster 0 to 1
                                    $this->addCustomersPoints($this->pointConfig['pointToAddCluster'], $c['id'], $this->pointConfig['descriptionScriptAddPointCluster'], $this->pointConfig['typeCluster']);
                                    if (!$this->avoidEmails) {
                                        $customer = $this->customerService->findById($c['id']);
                                        $this->sendEmail($customer->getEmail(), $customer->getName(), $customer->getLanguage(), 19);
                                    }
                                }
                            }
                        }
                    }
                }//end checkCustomerAlreadyAddPointsCluster
            }//end foreach

            $this->serverScriptService->writeEndServerScript($serverScriptCluster);

            $format = "%s;INF;addPointClusterAction;end\n";
            $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

        } catch (\Exception $e) {
            $this->writeServerScript($this->pointConfig['nameAddPointCluster'], $serverScriptCluster, null, $e, "ERROR");
            $format = "%s;ERROR;addPointClusterAction;end with Exception consult table server_scripts!\n";
            $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
        }
    }//end addPointClusterAction


    /*
     * this method check if customer have already received the cluster bonus
     */

    private function checkCustomerAlreadyAddPointsCluster($customerId) {
        $points = $this->customerService->getCustomerPointsCheckCluster($customerId);

        return (count($points) > 0) ? false : true;
    }

    private function checkCustomerIfAlreadyAddPointsThisMonth($customerId, $dateCurrentMonthStart, $dateNextMonthStart, $dateTodayStart) {

        $firstDayOfThisMonth = date('Y-m-01');
        $firstDayOfThisMonth = $firstDayOfThisMonth.(" 00:00:00");

        //Check if this date is the first day of this month
        if($dateTodayStart === $firstDayOfThisMonth){
            //if yes, i change the range date, now i see the last month
            $newDateMonthStart = new \DateTime($dateCurrentMonthStart);
            $newDateMonthStart = $newDateMonthStart->modify("-1 month");
            $newDateMonthStart = $newDateMonthStart->format("Y-m-d 00:00:00");

            return $this->customerService->checkCustomerIfAlreadyAddPointsThisMonth($customerId, $newDateMonthStart, $dateCurrentMonthStart);
        }else{
            return $this->customerService->checkCustomerIfAlreadyAddPointsThisMonth($customerId, $dateCurrentMonthStart, $dateNextMonthStart);
        }
    }

    /**
     * Check it the trips are closed near a pois then assign a bonus.
     *
     * If 'data-run' is empty (run by script), check the thips of the previous day, otherwise use the specified day (yyyy-mm-dd)
     */
    public function bonusPoisAction() {
        $this->prepareLogger();
        $request = $this->getRequest();
        $debug = $request->getParam('debug-mode') || $request->getParam('dm');
        $date_ts = $request->getParam('data-run');
        $radius = $request->getParam('radius');
        $carplate = $request->getParam('carplate');

        if(is_null($date_ts) || $date_ts=='') {
            $date_ts = date('Y-m-d', strtotime(' -1 day'));
        }

        $this->logger->log(sprintf("%s;INF;bonusPoisAction;start;debug=%s;date_ts=%s;;radius=%s;carplate=%s\n",
            date_create()->format('Y-m-d H:i:s'),
            $debug,
            $date_ts,
            $radius,
            $carplate
            ));

        $this->zoneBonusPark($date_ts, $radius, $carplate, $debug, 'POIS');
        $this->zoneBonusPark($date_ts, $radius, $carplate, $debug, 'POIS-FI-30');

        $this->logger->log(date_create()->format('Y-m-d H:i:s') . ";INF;bonusPoisAction;end\n");
    }

    /**
     *
     * @param datetime $date_ts Time stamp of trips
     * @param string $radius    Radius from pois in meters
     * @param string $carplate  Car plate
     * @param string $debug     Debug flag
     * @param integer $bonus_to_assign Total minutes of bonus
     * @param string $bonusType Type of bonus
     */
    private function zoneBonusPark($date_ts, $radius, $carplate, $debug, $bonusType) {
        //$debug = true;  //TODO REMOVE

        if($bonusType=='POIS') {
            $bonus_to_assign = 5;
            $duration =30;
            $description = 'Bonus parcheggio nei pressi di punto di ricarica - ';
            $batteryMinLevel = 25;
            $emailCategory = 16;
            $tripMinutes = 5;
            $fleets = array(1,4);   // only Milan and Modena
        } else if ($bonusType=='POIS-FI-30') {
            $bonus_to_assign = 30;
            $duration =30;
            $description = 'Parcheggio centro Firenze - ';
            $batteryMinLevel = null;
            $emailCategory = 23;    //TODO: change width new email
            $tripMinutes = null;
            $fleets = array(2);     // only Florence
        }
        else {
            $this->logger->log(date_create()->format('Y-m-d H:i:s') . ";WRN;zoneBonusPark;bonus type unknow;bonusType=".$bonusType."\n");
            return;
        }

        $this->logger->log(sprintf("%s;INF;zoneBonusPark;date_ts=%s;radius=%s;carplate=%s;debug=%s;bonus_to_assign=%s;bonusType=%s;duration=%s;description=%s;batteryMinLevel=%s;tripMinutes=%s\n",
            date_create()->format('Y-m-d H:i:s'),
            $date_ts,
            $radius,
            $carplate,
            $debug,
            $bonus_to_assign,
            $bonusType,
            $duration,
            $description,
            $batteryMinLevel,
            $tripMinutes));

        $tripsToBeComputed = $this->tripsService->getTripsForBonusParkComputation($date_ts, $carplate, $tripMinutes, $batteryMinLevel, $fleets);

        $this->logger->log(date_create()->format('Y-m-d H:i:s') . ";INF;zoneBonusPark;count=".count($tripsToBeComputed)."\n");

        foreach ($tripsToBeComputed as $trip) {

            // Verify if customer reached max amount in zone bonuses passed and return a list of those available
            $residuals = $this->poisService->checkPointInDigitalIslands($trip->getFleet()->getId(), $trip->getLatitudeEnd(), $trip->getLongitudeEnd(), $radius);
            if (count($residuals) == 0) {
                continue;
            }

            // Verify that only one bonus for trips with plate
            //$verified = $this->bonusService->verifyBonusPoisAssigned($trip->getCustomer(), $date_ts);
            $verified = $this->bonusService->verifyBonusPoisAssigned($trip->getCustomer(), date_create()->format('Y-m-d'));
            if (count($verified) > 0) {
                continue;
            }

            $this->logger->log(sprintf("%s;INF;zoneBonusPark;tripId=%s;customerId=%s;email=%s;carPlate=%s\n",
                date_create()->format('Y-m-d H:i:s'),
                $trip->getId(),
                $trip->getCustomer()->getId(),
                $trip->getCustomer()->getEmail(),
                $trip->getCar()->getPlate()));

            if ($debug) {
                continue;
            }

            // Assign bonuses to customer
            $this->assigneBonus($trip, $bonus_to_assign, $bonusType, $duration, $description . $trip->getCar()->getPlate());

            // send email to the customer
            $this->sendEmail(strtoupper($trip->getCustomer()->getEmail()), $trip->getCustomer()->getName(), $trip->getCustomer()->getLanguage(), $emailCategory);
        }

    }

    private function sendEmail($email, $name, $language, $category) {
        //$writeTo = $this->emailSettings['from'];
        $mail = $this->emailService->getMail($category, $language);
        $content = sprintf(
                $mail->getContent(), $name
        );

        //file_get_contents(__DIR__.'/../../../view/emails/parkbonus_pois-it_IT.html'),

        $attachments = [
                //'bannerphono.jpg' => __DIR__.'/../../../../../public/images/bannerphono.jpg'
        ];
        $this->emailService->sendEmail(
                $email, //send to
                $mail->getSubject(), //'Share’ngo: bonus 5 minuti',//object email
                $content, $attachments
        );
    }

    /*
     * Return the amount of extra payment
     */

    private function zoneExtraFareGetAmount(Trips $trip, array $zonesBonus, &$extraFareDescription) {
        $result = 0;

        try {
            if (count($zonesBonus) > 0) {   // if there are zone bonus
                //$this->logger->log(date_create()->format('y-m-d H:i:s').";INF;zoneExtraFareGetAmount;init;".$trip->getId().";".$trip->getLongitudeBeginning().";".$trip->getLatitudeBeginning().";".$result."\n");
                // check if the beginning of trip is inside of zoneBonus
                $zonesBonusInside = $this->zonesService->checkPointInBonusZones(
                        $zonesBonus, $trip->getLongitudeBeginning(), $trip->getLatitudeBeginning());

                if (count($zonesBonusInside) > 0) {
                    $result += intval($zonesBonusInside[0]->getCost());
                    $extraFareDescription = $zonesBonusInside[0]->getDescription();
                    $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;zoneExtraFareGetAmount;start;" . $trip->getId() . ";" . $zonesBonusInside[0]->getId() . ";" . $result . "\n");
                }

                // check if the end of trip is inside of zoneBonus
                $zonesBonusInside = $this->zonesService->checkPointInBonusZones(
                        $zonesBonus, $trip->getLongitudeEnd(), $trip->getLatitudeEnd());

                if (count($zonesBonusInside) > 0) {
                    $result += intval($zonesBonusInside[0]->getCost());
                    $extraFareDescription = $zonesBonusInside[0]->getDescription();
                    $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;zoneExtraFareGetAmount;end;" . $trip->getId() . ";" . $zonesBonusInside[0]->getId() . ";" . $result . "\n");
                }

                $events = $this->eventsService->getEventsByTrip($trip);
                foreach ($events as $event) {
                    if ($event->getEventId() == 3) {            // event RFID (parking)
                        if ($event->getIntval() == 3) {          // inval parking start
                            $zonesBonusInside = $this->zonesService->checkPointInBonusZones(
                                    $zonesBonus, $event->getLon(), $event->getLat());

                            if (count($zonesBonusInside) > 0) {
                                $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;zoneExtraFareGetAmount;parking;" . $trip->getId() . ";" . $zonesBonusInside[0]->getId() . ";" . $result . "\n");
                                $result += intval($zonesBonusInside[0]->getCost());
                                $extraFareDescription = $zonesBonusInside[0]->getDescription();
                            }
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            $this->logger->log(date_create()->format('y-m-d H:i:s') . ";ERR;zoneExtraFareGetAmount;" . $ex->getMessage() . "\n");
        }

        return $result;
    }

    /**
     * Add cost of extra fare to the trip
     *
     * @todo we need to account extra fare for business trip
     *
     * @param Trips $trip
     * @param string $extraFareDescription A description concatenate to address_beginnin and mark the extra payment alredy computed
     * @param int $extraFareAmount
     * @return boolean
     */
    private function zoneExtraFareAddAmount(Trips $trip, $extraFareDescription, $extraFareAmount) {
        $result = FALSE;

        try {
            if ($extraFareAmount > 0) {
                if (strlen($extraFareDescription) > 0) {
                    if ($trip->getPayable()) {
                        $pos = strpos($trip->getAddressBeginning(), $extraFareDescription);
                        if ($pos === false) { // check if the trip description dosn't contain already the reason
                            $this->tripsService->setAddressByGeocode($trip, false, " (" . $extraFareDescription . ")");
                            if($trip->isBusiness()) {
                                //TODO: we need to account extra fare for business trip
                                $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;zoneExtraFareAddAmount;B2B;" . $trip->getId() . ";" . $extraFareAmount . "\n");
                            } else {
                                $this->tripPaymentsService->setExtraFare($trip, $extraFareAmount);
                                $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;zoneExtraFareAddAmount;;" . $trip->getId() . ";" . $extraFareAmount . "\n");
                            }
                        }
                    }
                }
            }

            $result = TRUE;
        } catch (Exception $ex) {
            $this->logger->log(date_create()->format('y-m-d H:i:s') . ";ERR;zoneExtraFareApplyAmount;" . $ex->getMessage() . "\n");
        }
        return $result;
    }

    public function bonusNiveaAction() {

        $this->prepareLogger();
        $format = "%s;INF;bonusNiveaAction;strat\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

        $descriptionBonusNivea = "Courtesy of NIVEA";

        $customers = $this->customerService->getCustomerBonusNivea($descriptionBonusNivea);

        $date = date_create();
        $date2 = date_create('+ 30 day');

        foreach ($customers as $customer) {
            $bonus = new \SharengoCore\Entity\CustomersBonus();
            $bonus->setInsertTs($date);
            $bonus->setTotal(15);
            $bonus->setResidual(15);
            $bonus->setUpdateTs($date);
            $bonus->setValidFrom($date);
            $bonus->setValidTo($date2);
            $bonus->setDescription($descriptionBonusNivea);

            $this->customerService->addBonus($customer, $bonus);

            $format = "%s;INF;bonusNiveaAction;Customer_id= %d;Processed!\n";
            $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s'), $customer->getId()));

            $this->customerService->clearEntityManagerBonus();
        }

        $format = "%s;INF;bonusNiveaAction;end\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
    }

    public function BonusAlgebrisAction(){

        $this->prepareLogger();
        $format = "%s;INF;addBonusByAlgebris;strat\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');

        $format = "%s;INF;addBonusByAlgebris;";
        if (!$dryRun) {
            $format .= "DryRun = TRUE;";
        } else {
            $format .= "DryRun = FALSE;";
        }
        $format .= "\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

        $descriptionBonusAlgebris = "Courtesy of ALGEBRIS";

        $yesterday = new \DateTime();
        $yesterday = $yesterday->modify("-1 day");
        $yesterday = $yesterday->format("Y-m-d 00:00:00");

        $startMonth = new \DateTime($yesterday);
        $startMonth = $startMonth->modify("first day of this month");
        $startMonth = $startMonth->format("Y-m-d 00:00:00");

        $endMonth = new \DateTime($yesterday);
        $endMonth = $endMonth->modify("first day of next month");
        $endMonth = $endMonth->format("Y-m-d 00:00:00");

        $date_zero = new \DateTime("2018-04-01");
        $date_zero = $date_zero->format("Y-m-d 00:00:00");

        $customers = $this->customerService->getCustomerBonusAlgebris($descriptionBonusAlgebris, $startMonth, $endMonth);

        foreach ($customers as $customer) {
            if ($this->runBeforeDate($customer, $date_zero)) {
                if (!$dryRun) {
                    $bonus = new \SharengoCore\Entity\CustomersBonus();
                    $bonus->setInsertTs(date_create());
                    $bonus->setTotal(60);
                    $bonus->setResidual(60);
                    $bonus->setUpdateTs(date_create());
                    $bonus->setValidFrom(date_create());
                    $bonus->setValidTo(date_create('+ 60 day'));
                    $bonus->setType("bonus");
                    $bonus->setDescription($descriptionBonusAlgebris);

                    $this->customerService->addBonus($customer, $bonus);
                }

                $format = "%s;INF;addBonusByAlgebris;%d;%s\n";
                $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s'), $customer->getId(), $customer->getEmail()));
            }
            $this->customerService->clearEntityManagerBonus();
        }

        $format = "%s;INF;addBonusByAlgebris;end\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

    }

    public function runBeforeDate(Customers $customer, $date_zero) {
        $nTripBeforeAprilMonth = $this->customerService->checkIfCustomerRunBeforeDate($customer, $date_zero);
        return $nTripBeforeAprilMonth[0][1] == 0 ? true : false;
    }
    
    public function assignBonusCarFreeAction() {
        $this->prepareLogger();
        $format = "%s;INF;assignBonusCarFreeAction;strat\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
        
        $fleets = $this->fleetService->getAllFleetsNoDummy();
        foreach ($fleets as $fleet) {
            $format = "%s;INF;assignBonusCarFreeAction;Fleet: %s\n";
            $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s'), $fleet->getName()));
            
            $format = "%s;INF;assignBonusCarFreeAction;Call to operators...\n";
            $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s'), $fleet->getName()));
            $permanance_areas = file_get_contents('http://operators.sharengo.it/dev/get_permanency.php?city='.strtolower($fleet->getCode()).'&hour='.date('H'));
            
            $result = json_decode($permanance_areas);
            $result = get_object_vars($result);

            if(isset($result['Error'])){
                $format = "%s;ERR;assignBonusCarFreeAction;ERROR CALL TO OPERATORS\n";
                $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
            }else{
                $format = "%s;INF;assignBonusCarFreeAction;Success call to operators...\n";
                $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
                
                $matrix = $this->createMatrix($result['Value'], $this->positionConfig['l']);

                //recupero le macchine disponibili
                $cars = $this->carsService->getPublicCarsForAddFreeX($fleet->getId());
                foreach ($cars as $car) {
                    if ($car->getLongitude() > $this->positionConfig[$fleet->getName()]['start_lon'] && $car->getLongitude() < $this->positionConfig[$fleet->getName()]['end_lon'] && $car->getLatitude() - $this->positionConfig[$fleet->getName()]['start_lat'] && $car->getLatitude() - $this->positionConfig[$fleet->getName()]['end_lat']) {
                        $x = (int)floor(($car->getLongitude() - $this->positionConfig[$fleet->getName()]['start_lon']) / $this->positionConfig['dis_lon']);
                        $y = floor(($car->getLatitude() - $this->positionConfig[$fleet->getName()]['start_lat']) / $this->positionConfig['dis_lat']);
                        $permanance_car = (int)$matrix[$x][$y];

                        $freeX = null;
                        if($permanance_car <= $this->positionConfig['limit_free5']){
                            $freeX = 5;
                        } else {
                            if($permanance_car > $this->positionConfig['limit_free5'] && $permanance_car <= $this->positionConfig['limit_free10']) {
                                $freeX = 10;
                            } else {
                                    $freeX = 15; //$permanance_car > $this->positionConfig['limit_free15']
                            }
                        }
                        
                        //aggiunta in car bonus il campo freeX valorizzato secondo la permanenza
                        $car_bonus = $this->carsBonusService->findOneByPLate($car->getPlate());
                        $car_bonus = $this->carsBonusService->addFreeBonus($car_bonus, $freeX);
                        
                        $cars_bonus_history = $this->carsBonusHistoryService->createRecord($freeX, true , $car->getPlate());
                        
                    }
                }//end foreach cars
                
                //pulizia entity manager
                
            }
        }//end foreach fleets
        
        $format = "%s;INF;assignBonusCarFreeAction;end\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
        
    }
    
    private function createMatrix($permanance_areas, $side) {
        $matrix = array();
        for($i = 0; $i < count($permanance_areas)/$side; $i++){
            $row = array_slice($permanance_areas, $side*$i, $side);
            array_push($matrix, $row);
        }
        return $matrix;
    }


}
