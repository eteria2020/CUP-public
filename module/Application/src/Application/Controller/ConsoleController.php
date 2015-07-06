<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CarsService;
use SharengoCore\Service\ReservationsService;
use SharengoCore\Service\ReservationsArchiveService;
use SharengoCore\Entity\Reservations;
use Doctrine\ORM\EntityManager;
use Application\Service\ProfilingPlaformService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ConsoleController extends AbstractActionController
{


    const OPERATIVE = 'operative';

    const MAINTENANCE = 'out_of_order';

    const OPERATIVEACTION = 0;

    const MAINTENANCEACTION = 1;

    /**
     * @var boolean defines verbosity
     */
    private $verbose;

    /**
     * @var CustomersService
     */
    private $customerService;

    /**
     * @var CarsService
     */
    private $carsService;

    /**
     * @var ReservationsService
     */
    private $reservationsService;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ProfilingPlatformservice
     */
    private $profilingPlatformService;

    /**
     * @var string
     */
    private $battery;

    /**
     * @var string
     */
    private $delay;

    public function __construct(
        CustomersService $customerService,
        CarsService $carsService,
        ReservationsService $reservationsService,
        EntityManager $entityManager,
        ProfilingPlaformService $profilingPlatformService,
        $alarmConfig
    ) {
        $this->customerService = $customerService;
        $this->carsService = $carsService;
        $this->reservationsService = $reservationsService;
        $this->entityManager = $entityManager;
        $this->profilingPlatformService = $profilingPlatformService;
        $this->battery = $alarmConfig['battery'];
        $this->delay = $alarmConfig['delay'];
    }

    public function getDiscountsAction()
    {
        $customers = $this->customerService->getListCustomers();

        foreach ($customers as $customer) {
            if ($customer->getDiscountRate() == 0) {
                $email = $customer->getEmail();

                try {
                    $discount = $this->profilingPlatformService->getDiscountByEmail($email);
                } catch (\Exception $e) {
                    $discount = 0;
                }

                $this->customerService->setCustomerDiscountRate($customer, $discount);

                echo "customer done: ".$email."\n";
            }
        }

        echo "done\n";
    }

    public function assignBonusAction()
    {
        $customers = $this->customerService->getListCustomers();

        $startDateBonus100Mins = \DateTime::createFromFormat('Y-m-d H:i:s', '2015-06-14 23:59:59');
        $defaultBonusInsertDate = \DateTime::createFromFormat('Y-m-d H:i:s', '2015-01-01 00:00:00');
        $defaultBonusExpiryDate = \DateTime::createFromFormat('Y-m-d H:i:s', '2015-12-31 23:59:59');

        foreach ($customers as $customer) {

            // security check to avoid multiple script executions
            if (count($customer->getBonuses()) == 0) {

                $bonusValue = 100;
                $bonusDesc = 'Bonus iscrizione utente';
                if (null == $customer->getInsertedTs() ||
                    $customer->getInsertedTs() < $startDateBonus100Mins) {
                    $bonusValue = 500;
                    $bonusDesc = 'Bonus iscrizione utente prima del 15-06-2015';
                }

                //create Bonus
                $bonus = new \SharengoCore\Entity\CustomersBonus();
                $bonus->setInsertTs(null != $customer->getInsertedTs() ? $customer->getInsertedTs() : $defaultBonusInsertDate);
                $bonus->setUpdateTs($bonus->getInsertTs());
                $bonus->setTotal($bonusValue);
                $bonus->setResidual($bonusValue);
                $bonus->setValidFrom($bonus->getInsertTs());
                $bonus->setValidTo($defaultBonusExpiryDate);
                $bonus->setDescription($bonusDesc);

                $this->customerService->addBonus($customer, $bonus);

                echo $customer->getId() . "\n";

            }

        }

        echo "\n\nDONE\n";

    }

    public function checkAlarmsAction()
    {
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run');
        $this->verbose = $request->getParam('verbose') || $request->getParam('v');
        $carsToOperative = [];
        $carsToMaintenance = [];

        $this->writeToConsole("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // get all cars
        $cars = $this->getCarsNotReserved($this->carsService->getCarsEligibleForAlarmCheck());
        $this->writeToConsole("Cars number = " . count($cars) . "\n");

        foreach ($cars as $car) {
            $this->writeToConsole("\nCar: plate = " . $car->getPlate());
            $this->writeToConsole(" battery = " . $car->getBattery());
            $lastContact = $car->getLastContact() ? $car->getLastContact()->format('Y-m-d H:i:s') : '';
            $this->writeToConsole(" last time = " . $lastContact);
            $this->writeToConsole(" charging = " . (($car->getCharging()) ? 'true' : 'false') . "\n");

            // defines if car status should be saved
            $flagPersist = false;
            // defines if car should be in maintenance
            $isAlarm =  $car->getBattery() < $this->battery ||
                        time() - $car->getLastContact()->getTimestamp() > $this->delay * 60 ||
                        $car->getCharging();
            $this->writeToConsole("isAlarm = " . (($isAlarm) ? 'true' : 'false') . "\n");
            $status = $car->getStatus();
            $this->writeToConsole("status = " . $status . "\n");

            if ($isAlarm) {
                $this->sendAlarmCommand(self::MAINTENANCEACTION, $car);
                if ($status == self::OPERATIVE) {
                    $car->setStatus(self::MAINTENANCE);
                    $flagPersist = true;
                    if ($this->verbose) {
                        array_push($carsToMaintenance, $car->getPlate());
                    }
                    $this->writeToConsole("status changed to " . self::MAINTENANCE . "\n");
                }

            } elseif ($status == self::MAINTENANCE && !$isAlarm) {
                $car->setStatus(self::OPERATIVE);
                $this->sendAlarmCommand(self::OPERATIVEACTION, $car);
                $flagPersist = true;
                if ($this->verbose) {
                    array_push($carsToOperative, $car->getPlate());
                }
                $this->writeToConsole("status changed to " . self::OPERATIVE . "\n");

            }

            if ($flagPersist) {
                $this->entityManager->persist($car);
                $this->writeToConsole("Entity manager: car persisted\n");

            }

        }

        if (!$dryRun) {
            $this->writeToConsole("\nEntity manager: about to flush\n");
            $this->entityManager->flush();
            $this->writeToConsole("Entity manager: flushed\n");
        }

        if ($this->verbose) {
            $this->writeToConsole("\n\nStats:\n");
            $this->writeToConsole("\nCars set to " . self::OPERATIVE . ": " . count($carsToOperative) . "\n");
            foreach ($carsToOperative as $key => $value) {
                $this->writeToConsole("Plate: " . $value . "\n");
            }
            $this->writeToConsole("\nCars set to " . self::MAINTENANCE . ": " . count($carsToMaintenance) . "\n");
            foreach ($carsToMaintenance as $key => $value) {
                $this->writeToConsole("Plate: " . $value . "\n");
            }
        }

        $this->writeToConsole("\n\ndone\n\n");
    }

    /**
     * @param integer
     * @param Cars
     */
    private function sendAlarmCommand($alarmCode, $car)
    {
        $this->writeToConsole("Alarm code = " . $alarmCode . "\n");

        $reservations = $this->reservationsService->getActiveReservationsByCar($car->getPlate());
        $this->writeToConsole("reservations retrieved\n");

        if ($alarmCode == self::OPERATIVEACTION) {
            // remove current active reservation

            foreach ($reservations as $reservation) {
                $reservation->setActive(false)
                    ->setToSend(true);
                $this->writeToConsole("set reservation.active to false\n");
                $this->entityManager->persist($reservation);
                $this->writeToConsole("Entity manager: reservation persisted\n");
            }

        } elseif ($alarmCode == self::MAINTENANCEACTION) {

            if (count($reservations) == 0) {
                $this->writeToConsole("no reservation found, creating...\n");
                // create reservation for all maintainers
                $cardsArray = [];
                $maintainersCardCodes = $this->customerService->getListMaintainersCards();
                $this->writeToConsole("cards retrieved\n");
                // create single json string with all maintainer's card codes
                foreach ($maintainersCardCodes as $cardCode) {
                    //$this->writeToConsole("card code = " . $cardCode['1'] . " added\n");
                    array_push($cardsArray, $cardCode['1']);
                }
                $cardsString = json_encode($cardsArray);

                $reservation = new Reservations();
                $this->writeToConsole("reservation created\n");

                $reservation->setTs(date_create())
                    ->setCar($car)
                    ->setCustomer(null)
                    ->setBeginningTs(date_create())
                    ->setActive(true)
                    ->setLength(-1)
                    ->setToSend(true)
                    ->setCards($cardsString);

                $this->entityManager->persist($reservation);
                $this->writeToConsole("Entity manager: reservation persisted\n");

            } else {
                $this->writeToConsole("reservation found, skipping creation...\n");
            }

        }
    }

    private function getCarsNotReserved($cars)
    {
        $this->writeToConsole("Filtering cars by reservation...\n");
        $reservationsService = $this->reservationsService;
        $carsNotReserved = array_filter($cars, function ($car) use ($reservationsService) {
            $reservations = $reservationsService->getActiveReservationsByCar($car->getPlate());
            return empty($reservations) || ($reservations[0]->getLength() == -1);
        });

        $this->writeToConsole("Filtered cars by reservation\n");
        return $carsNotReserved;
    }

    public function archiveReservationsAction()
    {
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run');
        $this->verbose = $request->getParam('verbose') || $request->getParam('v');
        $reservationsDeleted = ['USED' => [], 'DELETED' => [], 'EXPIRED' => []];
        $reservationsArchived = [];

        $this->writeToConsole("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // get reservations to delete
        $reservations = $this->reservationsService->getReservationsToDelete();
        $this->writeToConsole("Retrieved reservations to delete: " . count($reservations) . "\n\n");

        foreach ($reservations as $reservation) {
            // output reservation info
            if ($this->verbose) {
                $this->writeToConsole("Reservation id: " . $reservation->getId());
                $this->writeToConsole(" consumed_ts: " . (($reservation->getConsumedTs() == null) ? "null" : $reservation->getConsumedTs()->format('Y-m-d H:i:s')) );
                $this->writeToConsole(" active: " . (($reservation->getActive()) ? 'true' : 'false') );
                $this->writeToConsole(" to_send: " . (($reservation->getToSend()) ? 'true' : 'false') );
                $this->writeToConsole(" beginning_ts: " . $reservation->getBeginningTs()->format('Y-m-d H:i:s'));
                $this->writeToConsole(" length: " . $reservation->getLength() . "\n");
            }

            // retrieve reason
            if ($reservation->getConsumedTs() != null) {
                $reason = 'USED';
            } elseif (!$reservation->getActive() && !$reservation->getToSend()) {
                $reason = 'DELETED';
            } else {
                $reason = 'EXPIRED';
            }
            $this->writeToConsole("Reason: " . $reason . "\n");

            // create reservationsArchive
            $archiveReservation = \SharengoCore\Entity\ReservationsArchive::createFromReservation($reservation, $reason);
            array_push($reservationsArchived, $archiveReservation->getId());
            $this->writeToConsole("Wrote to archive\n");
            // persist reservationsArchive
            $this->entityManager->persist($archiveReservation);
            $this->writeToConsole("EntityManager: reservationsArchive persisted\n");
            // remove reservation
            $this->entityManager->remove($reservation);
            array_push($reservationsDeleted[$reason], $reservation->getId());
            $this->writeToConsole("EntityManager: reservation removed\n\n");

        }

        if (!$dryRun) {
            $this->writeToConsole("EntityManager: about to flush...\n");
            $this->entityManager->flush();
            $this->writeToConsole("EntityManager: flushed\n\n");
        }

        if ($this->verbose) {
            $this->writeToConsole("Stats:\n");
            $this->writeToConsole("USED: " . count($reservationsDeleted['USED']) . "\n");
            $this->writeToConsole("DELETED: " . count($reservationsDeleted['DELETED']) . "\n");
            $this->writeToConsole("EXPIRED: " . count($reservationsDeleted['EXPIRED']) . "\n");
            $this->writeToConsole("Archived: " . count($reservationsArchived) . "\n\n");
        }

        $this->writeToConsole("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

    }

    private function writeToConsole($string)
    {
        if ($this->verbose) {
            fwrite(STDOUT, $string);
        }
    }

}
