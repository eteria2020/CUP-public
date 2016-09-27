<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\EditTripsService;
use SharengoCore\Service\BonusService;
use SharengoCore\Service\ZonesService;
use SharengoCore\Service\EventsService;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\ZoneBonus;
use SharengoCore\Entity\Trips;
use SharengoCore\Service\SimpleLoggerService as Logger;

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
     * @param CustomersService $customerService
     * @param TripsService $tripsService
     * @param EditTripsService $editTripService
     * @param BonusService $bonusService
     * @param ZonesService $zonesService
     * @param EventsService $eventsService 
     * @param Logger $logger
     * @param array $config
     */
    public function __construct(
        CustomersService $customerService,
        TripsService $tripsService,
        EditTripsService $editTripService,
        BonusService $bonusService,
        ZonesService $zonesService,
        EventsService $eventsService,
        Logger $logger,
        $config
    ) {
        $this->customerService = $customerService;
        $this->tripsService = $tripsService;
        $this->editTripService = $editTripService;
        $this->bonusService = $bonusService;
        $this->zonesService = $zonesService;
        $this->eventsService = $eventsService;
        $this->logger = $logger;
        $this->config = $config;
    }
    
    public function bonusComputeAction()
    {
        $this->prepareLogger();
        $this->checkDryRun();
        
        $this->logger->log("\nStarted computing for bonuses trips\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $this->zoneBonusCompute();    
    }

    public function zoneBonusCompute()
    {      
        $tripsToBeComputed = $this->tripsService->getTripsForBonusComputation();
        
        $this->logger->log("-------- Compute Zone Bonus\n\n");
        var_dump(count($tripsToBeComputed));
                
        foreach ($tripsToBeComputed as $trip) {

            if (!$trip instanceof Trips) {
                continue;
            }
            
            $this->logger->log("Computing bonus zone for trip " . $trip->getId() . ", fleet " . $trip->getFleet()->getId() . 
                ", customer " . $trip->getCustomer()->getId() .
                ", time " . $trip->getTimestampBeginning()->format('Y-m-d') . "\n");
            
            // Mettere a true $bonusComputed in trips
            $this->editTripService->doEditTripBonusComputed($trip, true);
            
            // Verifica se ci sono zone bonus su quella flotta
            $zonesBonus = $this->zonesService->getListZonesBonusByFleet($trip->getFleet());
            if (count($zonesBonus) == 0)
            {
                $this->logger->log("zonesBonus empty\n");
                continue;
            }
            
            $this->logger->log("Get Zones for fleet done\n");
            var_dump(count($zonesBonus));
                       
            // Verifica se il cliente ha raggiunto il tetto massimo nelle bonus area
            // e crea un elenco di quelle disponibili
            $residuals = $this->findBonusUsable($trip, $zonesBonus);            
            if (count($residuals) == 0)
            {
                $this->logger->log("residuals empty\n");
                continue;   
            }
            
            $this->logger->log("Get available Zones for Customer done\n");
            var_dump($residuals);           

            // Lettura eventi del trip per trovare le soste e analisi per vedere se danno bonus
            $this->verifyBonus($trip, $zonesBonus, $residuals);
            
            $this->logger->log("Computed new bonuses done\n");
            var_dump($residuals);
            
            // Assegnazione bonus al cliente
            foreach($residuals as $zone => $attribs)
            {
                if ($attribs["adding"] > 0)
                {
                    $this->assigneBonus($trip, $attribs["adding"], $zone, $attribs["duration"]);
                }
            }
        }
    }    
    
    private function verifyBonus(Trips $trip, array $zonesBonusByFleet, array &$residuals)
    {        
        $events = $this->eventsService->getEventsByTrip($trip);
        $time_beginning = 0;
        $is_bonus_parking = false;
        $bonus_attribs = 0;        
        
        foreach($events as $event)
        {
            // Cerco inizio sosta
            if ($event->getEventId() == 3 && $event->getIntval() == 3) // getLabel()
            {   
                $zonesBonus = $this->zonesService->checkPointInBonusZones(
                        $zonesBonusByFleet,
                        $event->getLon(),
                        $event->getLat());                
                
                if (count($zonesBonus) > 0)
                {
                    $this->logger->log("Bonus area find: ". $zonesBonus[0]->getDescription() ."\n");
                    
                    foreach($residuals as $zone => &$attribs)
                    {
                        if ($attribs["adding"] < $attribs["residual"] &&
                                strtolower($zone) === strtolower('zone-'.$zonesBonus[0]->getBonusType()))
                        {
                            $time_beginning = $event->getEventTime();
                            $bonus_attribs = &$attribs; //Per riferimento
                            $is_bonus_parking = true;
                            
                            break;
                        }
                    }
                }                
            }
            // Cerco fine sosta
            else if ($event->getEventId() == 3 && $event->getIntval() == 4) // getLabel()
            {
                if ($is_bonus_parking)
                {                           
                    $is_bonus_parking = false;
                    $time_ending = $event->getEventTime();
                    $minTime = new \DateTime('2016-01-01');
                    $int1 = $minTime->diff($time_beginning);
                    $int2 = $minTime->diff($time_ending);
                    
                    if ($int1 !== FALSE && $int2 !== FALSE)                                    
                    {
                        if ($int1->d > 0 && $int2->d > 0)
                        {
                            $parkInterval = $time_beginning->diff($time_ending);                            
                            if ($parkInterval->i > 0)
                            {
                                $maxBonus = $bonus_attribs["residual"] - $bonus_attribs["adding"];
                                if ($parkInterval->i >= $maxBonus)
                                {
                                    $bonus_attribs["adding"] = $bonus_attribs["residual"];
                                }
                                else
                                {
                                    $bonus_attribs["adding"] += $parkInterval->i;
                                }
                            }
                        }
                    }                            
                }                    
            }
        }
    }
    
    private function assigneBonus(Trips $trip, $bonus_to_assign, $bonus_type, $duration)
    {        
        $validFrom = $trip->getTimestampBeginning();
        $valFrom = $validFrom->format("Y-m-d");
        $validTo = new \DateTime($valFrom);
        $validTo->add(new \DateInterval('P'.strval($duration).'D')); //aggiungo i giorni di durata
        $valTo = $validTo->format("Y-m-d");       
        var_dump($valFrom);
        var_dump($valTo);
                
        $bonus = $this->bonusService->createBonusForCustomerFromData($trip->getCustomer(), $bonus_to_assign, $bonus_type, "Parking bonus", $valTo, $valFrom);
                
        $this->logger->log("Bonus assigned: " . $bonus->getId() . "\n");
        //var_dump($bonus);
    }
    
    private function findBonusUsable(Trips $trip, array $zonesBonus)
    {
        $residuals = array();
        
        foreach($zonesBonus as $zoneBonus)
        {
            $customerBonuses = $this->customerService->getBonusesForCustomerIdAndDateInsertionAndType(
                    $trip->getCustomer(),
                    $trip->getTimestampBeginning(),
                    'zone-'.$zoneBonus->getBonusType());
            
            var_dump(count($customerBonuses));
            //die;
            
            $zone_bonus_sum = 0;            
            foreach($customerBonuses as $customerBonus)
            {                
                $zone_bonus_sum += $customerBonus->getTotal();
            }
            
            $total = 30;
            $duration = 60;
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
                if (strtolower($zone) === strtolower($zoneBonus->getBonusType()))
                {
                    if (isset($attribs["total"]))
                    {                        
                        $total = $attribs["total"];                            
                    }
                    if (isset($attribs["duration"]))
                    {
                        $duration = $attribs["duration"];
                    }            
                    break;
                }
            }
            
            if ($zone_bonus_sum < $total)
            {             
                $residuals['zone-'.$zoneBonus->getBonusType()] = array (
                        "residual" => $total - $zone_bonus_sum,
                        "adding" => 0,
                        "duration" => $duration  
                    );
            }
        }
        return $residuals;
    }

    private function prepareLogger()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);
    }

    private function checkDryRun()
    {
        $request = $this->getRequest();
        //$this->avoidPersistance = $request->getParam('dry-run') || $request->getParam('d');
    }
}
