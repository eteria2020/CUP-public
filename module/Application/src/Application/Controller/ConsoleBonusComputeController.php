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

        $this->logger->log("\nStarted computing for bonuses trips\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $this->zoneBonusCompute();
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
            if ($trip->getCustomer()->getGoldList() || $trip->getCustomer()->getMaintainer()) {
                continue;
            }

            // Put to true $bonusComputed in trips
            $this->editTripService->doEditTripBonusComputed($trip, true);

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

            var_dump($residuals); //DEBUG

            // Read and process trip events to find stops for parking, contolling if they obtain zone bonuses
            $this->verifyBonus($trip, $zonesBonus, $residuals);

            var_dump($residuals); //DEBUG

            // Assign zone bonuses to customer
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
                                strtolower($zone) === strtolower($zonesBonus[0]->getBonusType()))
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

                        $int1 = ($time_beginning->getTimestamp() - $minTime->getTimestamp()) / 60;
                        $int2 = ($time_ending->getTimestamp() - $minTime->getTimestamp()) / 60;

                        if ($int1 > 0 && $int2 > 0 && $int2 > $int1)
                        {
                            $intstop = $int2 - $int1;

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

    private function assigneBonus(Trips $trip, $bonus_to_assign, $bonus_type, $duration)
    {
        $validFrom = $trip->getTimestampBeginning();
        $valFrom = $validFrom->format("Y-m-d");
        $validTo = new \DateTime($valFrom);
        $validTo->add(new \DateInterval('P'.strval($duration).'D')); //aggiungo i giorni di durata
        $valTo = $validTo->format("Y-m-d");

        $bonus = $this->bonusService->createBonusForCustomerFromData($trip->getCustomer(), $bonus_to_assign, 'zone-'.$bonus_type, "Parking bonus ".$bonus_type, $valTo, $valFrom);

        $this->logger->log("Bonus ".$bonus_type." assigned: ".$bonus->getId()." to customer ".$trip->getCustomer()->getId()."\n");
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
                $residuals[$zoneBonus->getBonusType()] = array (
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
}
