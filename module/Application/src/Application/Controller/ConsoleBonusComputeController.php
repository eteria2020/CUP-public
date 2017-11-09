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
use SharengoCore\Service\ServerScriptsService;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\ZoneBonus;
use SharengoCore\Entity\CustomersPoints;
use SharengoCore\Entity\Trips;
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param CustomersService $customerService
     * @param ServerScriptsService $serverScriptServic
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
     * @param EntityManager $entityManager
     */
    public function __construct(
    CustomersService $customerService, ServerScriptsService $serverScriptService, CarsService $carsService, TripsService $tripsService, TripPaymentsService $tripPaymentsService, EditTripsService $editTripService, BonusService $bonusService, ZonesService $zonesService, EmailService $emailService, PoisService $poisService, EventsService $eventsService, Logger $logger, $config, $pointConfig, Form $customerPointForm
    //, EntityManager $entityManager
    ) {
        $this->customerService = $customerService;
        $this->serverScriptService = $serverScriptService;
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
        //$this->entityManager = $entityManager;
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

        $this->logger->log("Bonus " . $bonus_type . " assigned: " . $bonus->getId() . " to customer " . $trip->getCustomer()->getId() . "\n");
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
                }
            }
            if($minuteTripsYesterday > $this->pointConfig['maxValPointDay']){
                $pointToAdd = $this->pointConfig['maxValPointDay'];
            }else{
                $pointToAdd = $minuteTripsYesterday;
            }
            $pointToAdd = $pointToAdd*3;
             
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

        }//end foreach custimers
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
    
    public function recalculatePointsSeptemberAction() {

        $this->prepareLogger();
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " - START recalculate Points September Script \n");
        
        //delete table customers_points
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " ------------- DELETE ALL RECORD CUSTOMERS_POINTS -------------\n");
        $this->customerService->deleteCustomersPoints('2017-08-31');
        
        //-------------------------SETTEMBRE------------------------------------
        $dateStartSett = '2017-09-18';
        $dateEndSett = '2017-10-01';
        
        $customersRunSet = $this->customerService->getAllCustomerRunInMonth($dateStartSett, $dateEndSett);
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " ------------- SATRT CUSTOMERS RUN IN SEPTEMBER -------------\n");

        $this->clicleOfCustomers($customersRunSet, $dateStartSett, $dateEndSett, 0);
        
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " ------------- END CUSTOMERS RUN IN SEPTEMBER -------------\n");
        
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " - END recalculate Points September Script \n");
        
    }
    
    public function recalculatePointsOctoberAction() {
        
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " - START recalculate Points October Script \n");
        
        //delete row customers_points of october
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " ------------- DELETE RECORD CUSTOMERS_POINTS OF OCTOBER-------------\n");
        $this->customerService->deleteCustomersPoints('2017-09-30');
        
        //-------------------------OTTOBRE--------------------------------------
        $dateStartOtt = '2017-10-01';
        $dateEndOtt = '2017-11-01';
        
        $customersRunOtt = $this->customerService->getAllCustomerRunInMonth($dateStartOtt, $dateEndOtt);
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " ------------- SATRT CUSTOMERS RUN IN OCTOBER -------------\n");
        
        $this->clicleOfCustomers($customersRunOtt, $dateStartOtt, $dateEndOtt, 1);
        
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " ------------- END CUSTOMERS RUN IN OCTOBER -------------\n");
        
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " - END recalculate Points October Script \n");

    }
    
    public function recalculatePointsNovemberAction() {
        
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " - START recalculate Points November Script \n");
        
        //delete row customers_points of november
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " ------------- DELETE RECORD CUSTOMERS_POINTS OF NOVEMBER-------------\n");
        $this->customerService->deleteCustomersPoints('2017-10-31');
        echo 'canc';
        
        //-------------------------NOVEMBRE-------------------------------------
        $dateStartNov = '2017-11-01';
        $todayStart = new \DateTime();
        $todayStart = $todayStart->format("Y-m-d");
        
        $customersRunOtt = $this->customerService->getAllCustomerRunInMonth($dateStartNov, $todayStart);
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " ------------- SATRT CUSTOMERS RUN IN NOVEMBER -------------\n");
        
        $this->clicleOfCustomers($customersRunOtt, $dateStartNov, $todayStart, 2);
        
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " ------------- END CUSTOMERS RUN IN NOVEMBER -------------\n");
        
        $this->logger->log(date_create()->format('Y-m-d H:i:s') . " - END recalculate Points November Script \n");

    }
    
    public function clicleOfCustomers($customers, $dateStart, $dateEnd, $param) {
        
        //set date for insert in customer_points
        switch ($param) {
            case 0://settembre
                $dateInsert = new \DateTime('2017-09-18 00:00:00');
                $dateUpdate = new \DateTime('2017-09-30 00:00:00');
                $dateValidTo = new \DateTime('2017-09-18 00:00:00');
                $dateValidTo = $dateValidTo->modify('+10 years');
                break;
            case 1://ottobre
                $dateInsert = new \DateTime('2017-10-01 00:00:00');
                $date = new \DateTime('2017-10-31 00:00:00');
                $dateUpdate = new \DateTime($date->format("Y-m-d 00:00:00"));
                $dateValidTo = new \DateTime('2017-10-01 00:00:00');
                $dateValidTo = $dateValidTo->modify('+10 years');
                break;
            case 2://novembre
                $dateInsert = new \DateTime('2017-11-01 00:00:00');
                $date = new \DateTime();
                $dateUpdate = new \DateTime($date->format("Y-m-d 00:00:00"));
                $dateValidTo = new \DateTime('2017-11-01 00:00:00');
                $dateValidTo = $dateValidTo->modify('+10 years');
                break;
        }
        
        foreach ($customers as $customer){
            
            $this->logger->log(date_create()->format('Y-m-d H:i:s') . " - id: " . $customer['id'] . " - processed! \n");
             
            $tripsDivisionDay = null;
            $tripsDivisionDay = null;
            $tripsDay = null;
            $pointToAddDay = 0;
            $totalPoint = 0;
            
            $tripsInMonth = $this->tripsService->getTripInMonth($customer['id'], $dateStart, $dateEnd);

            foreach ($tripsInMonth as $tripMonth){
                $endTx = $tripMonth->getEndTx();
                $endTx = $endTx->format("Y-m-d");
                $tripsDivisionDay[$endTx][]= $tripMonth;
            }
            
            if(count($tripsDivisionDay) > 0){
                foreach($tripsDivisionDay as $key => $tripsDay){
                    $minuteTripsYesterday = 0;
                    foreach ($tripsDay as $trip) {
                        $interval = new Interval($trip->getTimestampBeginning(), $trip->getTimestampEnd());
                        $minuteTripsYesterday += $interval->minutes();
                    }
                    if($minuteTripsYesterday > $this->pointConfig['maxValPointDay']){
                        $pointToAddDay = $this->pointConfig['maxValPointDay'];
                    }else{
                        $pointToAddDay = $minuteTripsYesterday;
                    }
                    $totalPoint += $pointToAddDay*3;
                }
            }
            //new line in customers_points
            $this->addNewLineCustomersPoints($totalPoint, $customer['id'], $dateInsert, $dateUpdate, $dateValidTo);

        }
        
    }
    
    private function addNewLineCustomersPoints($totalPoint, $customer_id, \DateTime $dateInsert, \DateTime $dateUpdate, \DateTime $dateValidTo){
        
        $customerPointTmp = new \SharengoCore\Entity\CustomersPoints();

        $customerPointTmp->setTotal($totalPoint);
        $customerPointTmp->setDescription("recalculate points script");
        $customerPointTmp->setResidual(0);
        $customerPointTmp->setType("DRIVE");
        $customerPointTmp->setValidFrom($dateInsert);
        $customerPointTmp->setValidTo($dateValidTo);
        $customerPointTmp->setInsertTs($dateInsert);
        $customerPointTmp->setUpdateTs($dateUpdate);
        
        $this->customerService->addCustomerPoint($customerPointTmp, $customer_id);
        
    }

    public function bonusPoisAction() {
        $this->prepareLogger();
        $request = $this->getRequest();
        $debug = $request->getParam('debug-mode') || $request->getParam('dm');
        $this->logger->log("\nStarted computing for POIS bonuses \ntime = " . date_create()->format('Y-m-d H:i:s') . "\n");

        if ($debug) {
            $this->logger->log("\n---- Debug mode ----\n");
        }
        $date_ts = $request->getParam('data-run');
        $radius = $request->getParam('radius');
        $carplate = $request->getParam('carplate');

        $this->logger->log("\nShell date: " . $date_ts . "\n");
        $this->logger->log("Radius: " . $radius . " meters\n\n");

        $this->zoneBonusPark($date_ts, $radius, $carplate, $debug);
    }

    private function zoneBonusPark($date_ts, $radius, $carplate, $debug) {
        $tripsToBeComputed = $this->tripsService->getTripsForBonusParkComputation($date_ts, $carplate);

        $this->logger->log("-------- Compute Zone Bonuses Park POIS\n");
        $this->logger->log("Trips to compute: " . count($tripsToBeComputed) . "\n\n");

        foreach ($tripsToBeComputed as $trip) {

            if (!$trip instanceof Trips) {
                continue;
            }

            if ($trip->getDurationMinutes() <= 5) {
                continue;
            }

            //($trip->getCustomer()->getGoldList() || $trip->getCustomer()->getMaintainer())
            // Verify if customer reached max amount in zone bonuses passed and return a list of those available
            $residuals = $this->poisService->checkPointInDigitalIslands($trip->getFleet()->getId(), $trip->getLatitudeEnd(), $trip->getLongitudeEnd(), $radius);
            if (count($residuals) == 0) {
                continue;
            }

            // Verify that only one bonus for trips with plate
            $verified = $this->bonusService->verifyBonusPoisAssigned($trip->getCar()->getPlate());
            if (count($verified) >= 1) {
                continue;
            }

            if ($debug) {
                $this->logger->log("Trip ID:" . $trip->getId() . "- Customer ID: " . $trip->getCustomer()->getId() . " - Carplate:" . $trip->getCar()->getPlate() . "\n\n");
                continue;
            }

            // Assign bonuses to customer
            $this->assigneBonus($trip, 5, 'POIS', 30, "Bonus parcheggio nei pressi di punto di ricarica - " . $trip->getCar()->getPlate());

            //send email to customer -> notification bonuses
            $this->logger->log("send email:" . $trip->getCustomer()->getEmail() . "\n");

            // send email to the customer
            $this->sendEmail(strtoupper($trip->getCustomer()->getEmail()), $trip->getCustomer()->getName(), $trip->getCustomer()->getLanguage(), 16);
        }

        //Recap bonus assigned

        $this->logger->log("\nEnd computing for POIS bonuses \ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
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
     * @param Trips $trip
     * @param type $extraFareBonusType
     * @param type $extraFareAmount
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
                            $this->tripPaymentsService->setExtraFare($trip, $extraFareAmount);
                            $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;zoneExtraFareApplyAmount;addAmount;" . $trip->getId() . ";" . $extraFareAmount . "\n");
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

}
