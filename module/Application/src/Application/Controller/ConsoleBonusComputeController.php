<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PoisService;
use SharengoCore\Service\EditTripsService;
use SharengoCore\Service\BonusService;
use SharengoCore\Service\ZonesService;
use SharengoCore\Service\EventsService;
use SharengoCore\Service\EmailService;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\ZoneBonus;
use SharengoCore\Entity\CustomersPoints;
use SharengoCore\Entity\Trips;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleBonusComputeController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customerService;

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
     * @var Config
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
     * @param CustomersService $customerService
     * @param TripsService $tripsService
     * @param TripPaymentsService $tripPaymentsService
     * @param EditTripsService $editTripService
     * @param BonusService $bonusService
     * @param ZonesService $zonesService
     * @param EventsService $eventsService
     * @param Logger $logger
     * @param array $config
     * @param Form $customerPointForm
     */
    public function __construct(
        CustomersService $customerService,
        TripsService $tripsService,
        TripPaymentsService $tripPaymentsService,
        EditTripsService $editTripService,
        BonusService $bonusService,
        ZonesService $zonesService,
        EmailService $emailService,
        PoisService $poisService,
        EventsService $eventsService,
        Logger $logger,
        $config,
        Form $customerPointForm,
        array $pointConfig
    ) {
        $this->customerService = $customerService;
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
        $this->pointConfig = $pointConfig;
    }

    public function bonusComputeAction()
    {
        $this->prepareLogger();

        $this->logger->log("\nStarted computing for bonuses trips\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $this->zoneBonusCompute(); // TODO: de-comment in production
        $this->zoneExtraFareCompute();
    }

    public function zoneBonusCompute()
    {
        $tripsToBeComputed = $this->tripsService->getTripsForBonusComputation();

        $this->logger->log("-------- Compute Zone Bonuses\n");
        $this->logger->log("Trips to compute: ".count($tripsToBeComputed)."\n\n");

        foreach ($tripsToBeComputed as $trip) {

            if (!$trip instanceof Trips) {
                continue;
            }

            // Put to true $bonusComputed in trips
            $this->editTripService->doEditTripBonusComputed($trip, true);

            if ($trip->getCustomer()->getGoldList() || $trip->getCustomer()->getMaintainer())
            {
                continue;
            }

            // Verify if there are zone bonuses in that fleet
            $zonesBonus = $this->zonesService->getListZonesBonusByFleet($trip->getFleet());
            if (count($zonesBonus) == 0)
            {
                continue;
            }

            // Verify if customer reached max amount in zone bonuses passed and return a list of those available
            $residuals = $this->findBonusUsable($trip, $zonesBonus);
            if (count($residuals) == 0)
            {
                continue;
            }

            // Read and process trip events to find stops for parking, contolling if they obtain zone bonuses
            $this->verifyBonus($trip, $zonesBonus, $residuals);

            // Assign zone bonuses to customer
            foreach($residuals as $zone => $attribs)
            {
                if ($attribs["adding"] > 0)
                {
                    $this->assigneBonus($trip, $attribs["adding"], $zone, $attribs["duration"], "Parking bonus ".$attribs["name"]);
                }
            }
        }
    }

    private function zoneExtraFareCompute()
    {
        $tripsToBeComputed =  $this->tripsService->getTripsForExtraFareComputation();
        $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;zoneExtraFareCompute;start;".count($tripsToBeComputed)."\n");
        $zonesBonus = $this->zonesService->getListZonesBonusForExtraFare();
        $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;zoneExtraFareCompute;zonesBonus;".count($zonesBonus)."\n");

        foreach ($tripsToBeComputed as $trip) {     // loop through trips
            $extraFareDescription = "";
            $extraFareAmount = $this->zoneExtraFareGetAmount($trip, $zonesBonus, $extraFareDescription);
            $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;zoneExtraFareCompute;amount;".$trip->getId().";".$extraFareAmount."\n");
            $this->zoneExtraFareAddAmount($trip, $extraFareDescription, $extraFareAmount);
        }

        $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;zoneExtraFareCompute;end;\n");

    }

    private function verifyBonus(Trips $trip, array $zonesBonusByFleet, array &$residuals)
    {
        $events = $this->eventsService->getEventsByTrip($trip);
        $time_beginning = 0;
        $is_bonus_parking = false;
        $bonus_attribs = 0;

        foreach($events as $event)
        {
            // Search stop begin
            if ($event->getEventId() == 3 && $event->getIntval() == 3) // getLabel()
            {
                $zonesBonus = $this->zonesService->checkPointInBonusZones(
                        $zonesBonusByFleet,
                        $event->getLon(),
                        $event->getLat());

                if (count($zonesBonus) > 0)
                {
                    foreach($residuals as $zone => &$attribs)
                    {
                        if ($attribs["adding"] < $attribs["residual"] &&
                                $zone === strtolower($zonesBonus[0]->getBonusType()))
                        {
                            $tb = $event->getEventTime();
                            if (isset($tb))
                            {
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
            else if ($event->getEventId() == 3 && $event->getIntval() == 4) // getLabel()
            {
                if ($is_bonus_parking)
                {
                    $is_bonus_parking = false;
                    $te = $event->getEventTime();
                    if (isset($te))
                    {
                        $time_ending = $te;
                        $minTime = new \DateTime('2016-01-01');

                        $int1 = $time_beginning->getTimestamp() - $minTime->getTimestamp();
                        $int2 = $time_ending->getTimestamp() - $minTime->getTimestamp();

                        if ($int1 > 0 && $int2 > 0 && $int2 > $int1)
                        {
                            $intstop = intval(floor(($int2 - $int1) / 60));
                            if ($intstop >= $bonus_attribs["minMinutes"])
                            {
                                if ($bonus_attribs["fixedBonus"] > 0)
                                    $intstop = $bonus_attribs["fixedBonus"];
                                $maxBonus = $bonus_attribs["residual"] - $bonus_attribs["adding"];
                                if ($intstop >= $maxBonus)
                                {
                                    $bonus_attribs["adding"] = $bonus_attribs["residual"];
                                }
                                else
                                {
                                    $bonus_attribs["adding"] += $intstop;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function assigneBonus(Trips $trip, $bonus_to_assign, $bonus_type, $duration, $description)
    {
        $validFrom = $trip->getTimestampBeginning();
        $valFrom = $validFrom->format("Y-m-d");
        $validTo = new \DateTime($valFrom);
        $validTo->add(new \DateInterval('P'.strval($duration).'D')); //aggiungo i giorni di durata
        $valTo = $validTo->format("Y-m-d");

        $bonus = $this->bonusService->createBonusForCustomerFromData($trip->getCustomer(), $bonus_to_assign, 'zone-'.$bonus_type, $description, $valTo, $valFrom);

        $this->logger->log("Bonus ".$bonus_type." assigned: ".$bonus->getId()." to customer ".$trip->getCustomer()->getId()."\n");
    }

    private function findBonusUsable(Trips $trip, array &$zonesBonus)
    {
        $residuals = array();

        $zonesBonusNoDuplicate = array();
        foreach($zonesBonus as $zoneBonus)
        {
            $notFound = true;
            foreach($zonesBonusNoDuplicate as $zb)
            {
                if (strtolower($zoneBonus->getBonusType()) === strtolower($zb->getBonusType()))
                {
                    $notFound = false;
                    break;
                }
            }
            if ($notFound)
            {
                $zonesBonusNoDuplicate[] = $zoneBonus;
            }
        }

        foreach($zonesBonusNoDuplicate as $zoneBonus)
        {
            $bonus_type = strtolower($zoneBonus->getBonusType());
            $customerBonuses = $this->customerService->getBonusesForCustomerIdAndDateInsertionAndType(
                    $trip->getCustomer(),
                    $trip->getTimestampBeginning(),
                    'zone-'.$bonus_type);

            $zone_bonus_sum = 0;
            foreach($customerBonuses as $customerBonus)
            {
                $zone_bonus_sum += $customerBonus->getTotal();
            }

            $total = 30;
            $duration = 60;
            $fixedBonus = 0;
            $minMinutes = 1;
            if (isset($this->config["defaultTotal"]))
            {
                $total = $this->config["defaultTotal"];
            }
            if (isset($this->config["defaultDuration"]))
            {
                $duration = $this->config["defaultDuration"];
            }
            foreach($this->config as $zone => $attribs)
            {
                if (strtolower($zone) === $bonus_type)
                {
                    if (isset($attribs["total"]))
                    {
                        $total = $attribs["total"];
                    }
                    if (isset($attribs["duration"]))
                    {
                        $duration = $attribs["duration"];
                    }
                    if (isset($attribs["fixedBonus"]))
                    {
                        $fixedBonus = $attribs["fixedBonus"];
                    }
                    if (isset($attribs["minMinutes"]))
                    {
                        $minMinutes = $attribs["minMinutes"];
                    }
                    break;
                }
            }

            if ($zone_bonus_sum < $total)
            {
                $residuals[$bonus_type] = array (
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
        foreach($zonesBonus as $zoneBonus)
        {
            foreach ($residuals as $zone => $attribs)
            {
                if (strtolower($zoneBonus->getBonusType()) === $zone)
                {
                    $zonesBonusInterested[] = $zoneBonus;
                    break;
                }
            }
        }
        $zonesBonus = $zonesBonusInterested;

        return $residuals;
    }

    private function prepareLogger()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);
    }

    /*
     * this method verify/calculate if one customer can receive point day
     */
    public function addPointDayAction(){        
        
        $dateYesterdayStart = new \DateTime();
        $dateYesterdayStart = $dateYesterdayStart->modify("-1 day");
        $dateYesterdayStart = $dateYesterdayStart->format("Y-m-d 00:00:00");
        
        $dateTodayStart = new \DateTime();
        $dateTodayStart = $dateTodayStart->format("Y-m-d 00:00:00");
        
        
        $dateCurrentMonthStart = new \DateTime('first day of this month');
        $dateCurrentMonthStart = $dateCurrentMonthStart->format("Y-m-d 00:00:00");
        
        $dateNextMonthStart = new \DateTime('first day of next month');
        $dateNextMonthStart = $dateNextMonthStart->format("Y-m-d 00:00:00");
        
        $customers= $this->customerService->getCustomersRunYesterday($dateYesterdayStart, $dateTodayStart);
        
        foreach ($customers as $c){
            
            $tripsYesterday = $this->tripsService->getTripsByCustomerForAddPointYesterday($c['id'], $dateYesterdayStart, $dateTodayStart);
            $tripsMonth = $this->tripsService->getTripsByCustomerForAddPointMonth($c['id'], $dateCurrentMonthStart, $dateYesterdayStart);
            
            $secondsTripsMonth = 0;
            $secondsTripsYesterday = 0;
            
            if(count($tripsMonth)>0)
                foreach ($tripsMonth as $tripMonth){
                    $timeTripsMonth = date_diff($tripMonth->getTimestampEnd(),$tripMonth->getTimestampBeginning());
                    $secondsTripsMonth += $this->calculateTripInSecond($timeTripsMonth);
                }
            
            if(count($tripsYesterday))
                foreach ($tripsYesterday as $tripYesterday){
                    $timeTripsYesterday = date_diff($tripYesterday->getTimestampEnd(),$tripYesterday->getTimestampBeginning());
                    $secondsTripsYesterday += $this->calculateTripInSecond($timeTripsYesterday);
                }
              
            $minuteTripsMonth = round($secondsTripsMonth/60, 0);
            $minuteTripsYesterday = round($secondsTripsYesterday/60, 0);
            
            $result[0] = 0;
            $result[1] = $minuteTripsYesterday;
            $result[2] = $minuteTripsMonth;
            
            do{
                $result = $this->howManyPointsAddToUser($result);
            }while($result[1]>0);
            
            //add point in customers_points
            $this->addCustomersPoints($result[0], $c['id'], $this->pointConfig['descriptionScriptAddPointDay'], $this->pointConfig['typeDrive']);
            
        }//end foreach custimers
    }
    
    /*
     * create obj customer and add to db
     * for clas customerPoint Form, FormFactory e Fieldset
     * are copied to the same class from admin
     */
    private function addCustomersPoints($numeberAddPoint, $customerId, $nameScript, $type){
        $point = $this->customerPointForm;
        $data = $this->customerService->setPointField1($numeberAddPoint, $nameScript);
        $point->setData($data);
        $this->customerService->setPointField2($point->getData(), $customerId, $type);
    }

    /*
     * this method calculate how many point add to customer
     */
    private function howManyPointsAddToUser($result) {
        
        if(result[2] < $this->pointConfig['range1AddPointDay']){
            if((result[2] + result[1]) < $this->pointConfig['range1AddPointDay']){
                $result[0] += $result[1]*$this->pointConfig['multiplierRange0AddPointDay'];
                $result[1] = -1;
            }else{
                $result[0] += ($this->pointConfig['range1AddPointDay'] - $result[2])*$this->pointConfig['multiplierRange0AddPointDay'];
                $result[1] = $result[1] - ($this->pointConfig['range1AddPointDay'] - $result[2]);
                $result[2] = $this->pointConfig['range1AddPointDay'];
            }
            return $result;     
        }else{
            if($result[2] >= $this->pointConfig['range1AddPointDay'] && $result[2] < $this->pointConfig['range2AddPointDay']){
                if(($result[2] + $result[1]) < $this->pointConfig['range2AddPointDay']){
                    $result[0] += $result[1]*$this->pointConfig['multiplierRange1AddPointDay'];
                    $result[1] = -1;
                }else{
                    $result[0] += ($this->pointConfig['range2AddPointDay'] - $result[2])*$this->pointConfig['multiplierRange1AddPointDay'];
                    $result[1] = $result[1] - ($this->pointConfig['range2AddPointDay'] - $result[2]);
                    $result[2] = $this->pointConfig['range2AddPointDay'];
                }
                return $result;
            }else{
                if($result[2] >= $this->pointConfig['range2AddPointDay'] && $result[2] < $this->pointConfig['range3AddPointDay']){
                    if(($result[2] + $result[1]) < $this->pointConfig['range3AddPointDay']){
                        $result[0] += $result[1]*$this->pointConfig['multiplierRange2AddPointDay'];
                        $result[1] = -1;
                    }else{
                        $result[0] += ($this->pointConfig['range3AddPointDay'] - $result[2])*$this->pointConfig['multiplierRange2AddPointDay'];
                        $result[1] = $result[1] - ($this->pointConfig['range3AddPointDay'] - $result[2]);
                        $result[2] = $this->pointConfig['range3AddPointDay'];
                    }
                    return $result;
                }else{
                    if($result[2] >= $this->pointConfig['range3AddPointDay']){
                        $result[0] += $result[1]*$this->pointConfig['multiplierRange3AddPointDay'];
                        $result[1] = -1;
                        return $result;
                    }//end else if 600
                }//end else if between 200 and 600
            }//end else if between 80 and 200
        }//end else if 80
    }//end howManyPointsAddToUser
    
    /*
     * this method verify if one customer can receive this bonus
     */
    public function  addPointClusterAction(){
        
        $dateStartCurrentMonth = new \DateTime();
        $dateStartCurrentMonth = $dateStartCurrentMonth->format("Y-m-d 00:00:00");
        
        $dateStartLastMonth = new \DateTime();
        $dateStartLastMonth = $dateStartLastMonth->modify("-1 month");
        $dateStartLastMonth = $dateStartLastMonth->format("Y-m-d 00:00:00");
        
        $dateStartTwotMonthAgo = new \DateTime();
        $dateStartTwotMonthAgo = $dateStartTwotMonthAgo->modify("-2 month");
        $dateStartTwotMonthAgo = $dateStartTwotMonthAgo->format("Y-m-d 00:00:00");
        
        $customers= $this->customerService->getCustomersRunThisMonth($dateStartLastMonth, $dateStartCurrentMonth);
        
        foreach ($customers as $c){
            
            if(!$this->checkCustomerAlreadyAddPointsCluster($c['id'])){
                $tripsLastMonth = $this->tripsService->getTripsByCustomerForAddPointClusterLastMonth($c['id'], $dateStartLastMonth, $dateStartCurrentMonth);
                $secondsTripsLastMonth = 0;
                if(count($tripsLastMonth)>0)
                    foreach ($tripsLastMonth as $tripLastMonth){
                        $timeTripsLastMonth = date_diff($tripLastMonth->getTimestampEnd(), $tripLastMonth->getTimestampBeginning());
                        $secondsTripsLastMonth += $this->calculateTripInSecond($timeTripsLastMonth);
                    }
                $minuteTripsLastMonth = round($secondsTripsLastMonth/60, 0);

                if($minuteTripsLastMonth >= pointConfig['newCheckPointCluster']){
                    $tripsTwotMonthAgo = $this->tripsService->getTripsByCustomerForAddPointClusterTwotMonthAgo($c['id'], $dateStartLastMonth, $dateStartTwotMonthAgo);
                    $secondsTripsTwotMonthAgo = 0;
                    if(count($tripsTwotMonthAgo))
                        foreach ($tripsTwotMonthAgo as $tripTwotMonthAgo){
                            $timeTripsTwotMonthAgo = date_diff($tripTwotMonthAgo->getTimestampEnd(), $tripTwotMonthAgo->getTimestampBeginning());
                            $secondsTripsTwotMonthAgo += $this->calculateTripInSecond($timeTripsTwotMonthAgo);
                        }
                    $minuteTripsTwotMonthAgo = round($secondsTripsTwotMonthAgo/60, 0);
                    if($minuteTripsTwotMonthAgo < $this->pointConfig['oldCheckPointCluster']){
                       //add 80 points for pass cluster 0 to 1
                       $this->addCustomersPoints($this->pointConfig['pointToAddCluster'], $c['id'], $this->pointConfig['descriptionScriptAddPointCluster'], $this->pointConfig['typeCluster']);
                    }
                }

            }//end checkCustomerAlreadyAddPointsCluster
        }//end foreach
        echo "\nend script...\n";
    }//end addPointClusterAction
    
    
    /*
     * this method check if customer have already received the cluster bonus
     */
    private function checkCustomerAlreadyAddPointsCluster($customerId){
        $points = $this->customerService->getCustomerPointsByCustomer($customerId);
        if(count($points) > 0){
            foreach ($points as $p){
                $a = strtoupper($p->getType());
                if(strtoupper($p->getType()) == $this->pointConfig['typeCluster'])
                   return true; 
            }//end foreach $trips
        }
        return false;
    }
    
    /*
     * this method calculate in second how much one cluster
     * have runnig with param
     * Param is an object DateInterval
     */
    private function calculateTripInSecond($timeTrip) {
        
        $seconds = 0;
        
        $days = $timeTrip->format('%a');
        if ($days) {
            $seconds += 24 * 60 * 60 * $days;
        }
        $hours = $timeTrip->format('%H');
        if ($hours) {
            $seconds += 60 * 60 * $hours;
        }
        $minutes = $timeTrip->format('%i');
        if ($minutes) {
            $seconds += 60 * $minutes;
        }
        $seconds += $timeTrip->format('%s');
        
        return $seconds;
    }//end calculateTripInSecond

    public function bonusPoisAction(){
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

        $this->logger->log("\nShell date: ".$date_ts."\n");
        $this->logger->log("Radius: ".$radius." meters\n\n");

        $this->zoneBonusPark($date_ts, $radius, $carplate, $debug);
    }
    private function zoneBonusPark($date_ts, $radius, $carplate, $debug)
    {
        $tripsToBeComputed = $this->tripsService->getTripsForBonusParkComputation($date_ts, $carplate);

        $this->logger->log("-------- Compute Zone Bonuses Park POIS\n");
        $this->logger->log("Trips to compute: ".count($tripsToBeComputed)."\n\n");

        foreach ($tripsToBeComputed as $trip) {

            if (!$trip instanceof Trips) {
                continue;
            }

            if ($trip->getDurationMinutes()<=5){
                continue;
            }

            //($trip->getCustomer()->getGoldList() || $trip->getCustomer()->getMaintainer())

            // Verify if customer reached max amount in zone bonuses passed and return a list of those available
            $residuals = $this->poisService->checkPointInDigitalIslands($trip->getFleet()->getId(), $trip->getLatitudeEnd(), $trip->getLongitudeEnd(), $radius);
            if (count($residuals) == 0){
                continue;
            }

            // Verify that only one bonus for trips with plate
            $verified  = $this->bonusService->verifyBonusPoisAssigned($trip->getCar()->getPlate());
            if (count($verified)>=1){
                continue;
            }

            if ($debug){
                $this->logger->log("Trip ID:". $trip->getId() ."- Customer ID: ".$trip->getCustomer()->getId()." - Carplate:". $trip->getCar()->getPlate() ."\n\n");
                continue;
            }

            // Assign bonuses to customer
            $this->assigneBonus($trip, 5, 'POIS', 30, "Bonus parcheggio nei pressi di punto di ricarica - ".$trip->getCar()->getPlate());

            //send email to customer -> notification bonuses
            $this->logger->log("send email:".$trip->getCustomer()->getEmail()."\n");

            // send email to the customer
            $this->sendEmail(strtoupper($trip->getCustomer()->getEmail()), $trip->getCustomer()->getName(), $trip->getCustomer()->getLanguage());
        }

        //Recap bonus assigned

        $this->logger->log("\nEnd computing for POIS bonuses \ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
        private function sendEmail($email, $name, $language)
    {
        //$writeTo = $this->emailSettings['from'];
        $mail = $this->emailService->getMail(16, $language);
        $content = sprintf(
            $mail->getContent(),
            $name
        );

        //file_get_contents(__DIR__.'/../../../view/emails/parkbonus_pois-it_IT.html'),

        $attachments = [
            //'bannerphono.jpg' => __DIR__.'/../../../../../public/images/bannerphono.jpg'
        ];
        $this->emailService->sendEmail(
            $email, //send to
            $mail->getSubject(), //'Share’ngo: bonus 5 minuti',//object email
            $content,
            $attachments
        );
    }

    /*
     * Return the amount of extra payment
     */
    private function zoneExtraFareGetAmount(Trips $trip, array $zonesBonus, &$extraFareDescription){
        $result = 0;

        try {
            if(count($zonesBonus)>0){   // if there are zone bonus
                //$this->logger->log(date_create()->format('y-m-d H:i:s').";INF;zoneExtraFareGetAmount;init;".$trip->getId().";".$trip->getLongitudeBeginning().";".$trip->getLatitudeBeginning().";".$result."\n");
                // check if the beginning of trip is inside of zoneBonus
                $zonesBonusInside = $this->zonesService->checkPointInBonusZones(
                    $zonesBonus,
                    $trip->getLongitudeBeginning(),
                    $trip->getLatitudeBeginning());

                if(count($zonesBonusInside) > 0){
                    $result += intval($zonesBonusInside[0]->getCost());
                    $extraFareDescription = $zonesBonusInside[0]->getDescription();
                    $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;zoneExtraFareGetAmount;start;".$trip->getId().";".$zonesBonusInside[0]->getId().";".$result."\n");
                }

                // check if the end of trip is inside of zoneBonus
                $zonesBonusInside = $this->zonesService->checkPointInBonusZones(
                    $zonesBonus,
                    $trip->getLongitudeEnd(),
                    $trip->getLatitudeEnd());

                if(count($zonesBonusInside) > 0){
                    $result += intval($zonesBonusInside[0]->getCost());
                    $extraFareDescription = $zonesBonusInside[0]->getDescription();
                    $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;zoneExtraFareGetAmount;end;".$trip->getId().";".$zonesBonusInside[0]->getId().";".$result."\n");
                }

                $events = $this->eventsService->getEventsByTrip($trip);
                foreach($events as $event)
                {
                    if ($event->getEventId() == 3) {            // event RFID (parking)
                        if ($event->getIntval() == 3) {          // inval parking start

                            $zonesBonusInside = $this->zonesService->checkPointInBonusZones(
                                $zonesBonus,
                                $event->getLon(),
                                $event->getLat());

                            if(count($zonesBonusInside) > 0){
                                $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;zoneExtraFareGetAmount;parking;".$trip->getId().";".$zonesBonusInside[0]->getId().";".$result."\n");
                                $result += intval($zonesBonusInside[0]->getCost());
                                $extraFareDescription = $zonesBonusInside[0]->getDescription();
                            }
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            $this->logger->log(date_create()->format('y-m-d H:i:s').";ERR;zoneExtraFareGetAmount;".$ex->getMessage()."\n");
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
    private function zoneExtraFareAddAmount(Trips $trip, $extraFareDescription, $extraFareAmount){
        $result = FALSE;

        try {
            if($extraFareAmount > 0){
                if(strlen($extraFareDescription) > 0){
                    if($trip->getPayable()){
                        $pos = strpos($trip->getAddressBeginning(), $extraFareDescription);
                        if($pos === false){ // check if the trip description dosn't contain already the reason
                            $this->tripsService->setAddressByGeocode($trip, false, " (" . $extraFareDescription .")");
                            $this->tripPaymentsService->setExtraFare($trip, $extraFareAmount);
                            $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;zoneExtraFareApplyAmount;addAmount;".$trip->getId().";".$extraFareAmount."\n");
                        }
                    }
                }
            }

            $result = TRUE;
        } catch (Exception $ex) {
            $this->logger->log(date_create()->format('y-m-d H:i:s').";ERR;zoneExtraFareApplyAmount;".$ex->getMessage()."\n");
        }
        return $result;
    }

}