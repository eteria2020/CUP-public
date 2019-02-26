<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\AccountTripsService;
use SharengoCore\Service\CarsService;
use SharengoCore\Service\ReservationsService;
//use SharengoCore\Service\ReservationsArchiveService;
use SharengoCore\Entity\Reservations;
use Doctrine\ORM\EntityManager;
use Application\Service\ProfilingPlaformService;
use SharengoCore\Service\InvoicesService;
use Zend\Mvc\Controller\AbstractActionController;
use Cartasi\Service\CartasiContractsService;
use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Service\EmailService;
use SharengoCore\Service\CustomerDeactivationService;

class ConsoleController extends AbstractActionController {

    const OPERATIVE_STATUS = 'operative';
    const NON_OPERATIVE_STATUS = 'out_of_order';
    const MAINTENANCE_STATUS = 'maintenance';
    const OPERATIVE_ACTION = 0;
    const MAINTENANCE_ACTION = 1;

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
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var AccountTripsService
     */
    private $accountTripsService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var string
     */
    private $battery;

    /**
     * @var string
     */
    private $batterySpecific;

    /**
     * @var string
     */
    private $delay;

    /**
     * @var string
     */
    private $batteryUnplug;

    /**
     * @var CartasiContractsService
     */
    private $cartasiContractsService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var CustomerDeactivationService
     */
    private $customerDeactivationService;

    /**
     * @var boolean
     */
    private $avoidEmails;

    /**
     * @var array
     */
    private $alarmConfigSpecific;

    /**
     * @param CustomersService $customerService
     * @param CartasiContractsService $cartasiContractsService
     * @param Logger $logger
     * @param EmailService $emailService
     * @param CustomerDeactivationService $customerDeactivationService
     * @param CarsService $carsService
     * @param ReservationsService $reservationsService
     * @param EntityManager $entityManager
     * @param ProfilingPlaformService $profilingPlatformService
     * @param TripsService $tripsService
     * @param AccountTripsService $accountTripsService
     * @param array $alarmConfig
     * @param array $alarmConfigSpecific
     * @param InvoicesService $invoicesService
     */
    public function __construct(
        CustomersService $customerService,
        CartasiContractsService $cartasiContractsService,
        Logger $logger,
        EmailService $emailService,
        CustomerDeactivationService $customerDeactivationService,
        CarsService $carsService,
        ReservationsService $reservationsService,
        EntityManager $entityManager,
        ProfilingPlaformService $profilingPlatformService,
        TripsService $tripsService,
        AccountTripsService $accountTripsService,
        $alarmConfig,
        $alarmConfigSpecific,
        InvoicesService $invoicesService
    ) {
        $this->customerService = $customerService;
        $this->carsService = $carsService;
        $this->reservationsService = $reservationsService;
        $this->entityManager = $entityManager;
        $this->profilingPlatformService = $profilingPlatformService;
        $this->tripsService = $tripsService;
        $this->accountTripsService = $accountTripsService;
        $this->battery = $alarmConfig['battery'];
        $this->batterySpecific = json_decode($alarmConfigSpecific['battery'], true);
        $this->delay = $alarmConfig['delay'];
        $this->batteryUnplug = intval($alarmConfig['unplug_enable']);
        $this->alarmConfigSpecific = $alarmConfigSpecific;
        $this->invoicesService = $invoicesService;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->logger = $logger;
        $this->emailService = $emailService;
        $this->customerDeactivationService = $customerDeactivationService;
    }

    public function getDiscountsAction() {
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

                echo "customer done: " . $email . "\n";
            }
        }

        echo "done\n";
    }

    public function assignBonusAction() {
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

                // create Bonus
                $bonus = new \SharengoCore\Entity\CustomersBonus();
                $bonus->setInsertTs(null != $customer->getInsertedTs() ? $customer->getInsertedTs() : $defaultBonusInsertDate);
                $bonus->setTotal($bonusValue);
                $bonus->setResidual($bonusValue);
                $bonus->setUpdateTs($bonus->getInsertTs());
                $bonus->setValidFrom($bonus->getInsertTs());
                $bonus->setValidTo($defaultBonusExpiryDate);
                $bonus->setDescription($bonusDesc);

                $this->customerService->addBonus($customer, $bonus);

                echo $customer->getId() . "\n";
            }
        }

        echo "\n\nDONE\n";
    }

    public function checkAlarmsAction() {
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $this->verbose = $request->getParam('verbose') || $request->getParam('v');
        $carsToOperative = [];
        $carsToMaintenance = [];
        $batterySafetyTime = 1;

        $this->writeToConsole(date('ymd-His').";INF;checkAlarmsAction;start;\n");

        // get all cars without reservation or with maintenance/non_operative reservation
        $cars = $this->carsService->getCarsEligibleForAlarmCheck();

        $strLog = sprintf("%s;INF;checkAlarmsAction;dry-run=%s;verbose=%s;cars=%d;unplug=%d\n",
            date('ymd-His'),
            $dryRun,
            $this->verbose,
            count($cars),
            $this->batteryUnplug);

        $this->writeToConsole($strLog);

        foreach ($cars as $car) {
            $softwareVerNum = $car->getSoftwareVersionNumber();
            $firmwareVerNum = $car->getFirmwareVersionNumber();

            // defines if car status should be saved
            $flagPersist = false;
            // holds the car's status
            $status = $car->getStatus();

            // define chargin for unplug feature
            $charging = $car->getCharging();
            if($softwareVerNum >=10800) {  //TODO: condizione unplug, da aggiornare prima di mettere in produzione
                $charging = ($car->getCharging() &&
                    ($car->getBattery() < $this->batteryUnplug || !$car->getCarsBonusUnplugEnable()));
            }

            // defines if car should be in non_operative || is in maintenance
            $isAlarm = $this->checkBatteryConditions($car) || //other cities battery check
                time() - $car->getLastContact()->getTimestamp() > $this->delay * 60 ||
                $charging ||
                $this->carsService->isCarOutOfBounds($car) ||
                $status == self::MAINTENANCE_STATUS;

            //$this->writeToConsole("1. isAlarm = " . (($isAlarm) ? 'true' : 'false') . "\n");

            //check only for battery safety cars
            /*if (!$isAlarm && $firmwareVerNum>= 4730 && $softwareVerNum >=10670){
                if(is_null($car->getBatterySafetyTs())){
                    $isAlarm = false;
                } else {
                    $isAlarm = (!$car->getBatterySafety() && ((time() - $car->getBatterySafetyTs()->getTimestamp()) > $batterySafetyTime * 60));
                }
            }*/
            //$this->writeToConsole("2. isAlarm = " . (($isAlarm) ? 'true' : 'false') . "\n");
            //check only for nogps
            if (!$isAlarm && $softwareVerNum >=10700){
                $isAlarm = $car->getNogps() == true;
            }

            $strLog = sprintf("%s;INF;checkAlarmsAction;plate=%s;bat=%s;ver s/f=%s/%s;last=%s;charging=%s;out bounds=%s;alarm=%s;unplug=%s;status=%s\n",
                date('ymd-His'),
                $car->getPlate(),
                $car->getBattery(),
                $softwareVerNum,
                $firmwareVerNum,
                (($car->getLastContact()) ? $car->getLastContact()->format('Y-m-d H:i:s') : ''),
                (($car->getCharging()) ? 'YES' : 'NO'),
                (($this->carsService->isCarOutOfBounds($car)) ? 'YES':'NO'),
                (($isAlarm) ? 'YES' : 'NO'),
                (($car->getBattery() < $this->batteryUnplug || !$car->getCarsBonusUnplugEnable()) ? 'YES' : 'NO'),
                $status
                );

            $this->writeToConsole($strLog);
            // the car should have a maintainer's reservation
            if ($isAlarm) {
                // create reservation if !exists
                $this->sendAlarmCommand(self::MAINTENANCE_ACTION, $car);
                // the car should be non_operative
                if ($status == self::OPERATIVE_STATUS) {
                    // change the car's status to non_operative
                    $car->setStatus(self::NON_OPERATIVE_STATUS);
                    $flagPersist = true;
                    if ($this->verbose) {
                        array_push($carsToMaintenance, $car->getPlate());
                    }
                    $this->writeToConsole(date('ymd-His').";INF;checkAlarmsAction;status changed to " . self::NON_OPERATIVE_STATUS . "\n");
                }
                // the car should be operative
            } elseif ($status == self::NON_OPERATIVE_STATUS) {
                // change the car's status to operative
                $car->setStatus(self::OPERATIVE_STATUS);
                // remove active reservations
                $this->sendAlarmCommand(self::OPERATIVE_ACTION, $car);
                $flagPersist = true;
                if ($this->verbose) {
                    array_push($carsToOperative, $car->getPlate());
                }
                $this->writeToConsole(date('ymd-His').";INF;checkAlarmsAction;status changed to " . self::OPERATIVE_STATUS . "\n");
            }

            if ($flagPersist) {
                $this->entityManager->persist($car);
                $this->writeToConsole(date('ymd-His').";INF;checkAlarmsAction;Entity manager: car persisted;\n");
            }
        }

        if (!$dryRun) {
            $this->writeToConsole(date('ymd-His').";INF;checkAlarmsAction;Entity manager: about to flush;\n");
            $this->entityManager->flush();
            $this->writeToConsole(date('ymd-His').";INF;checkAlarmsAction;Entity manager: flushed;\n");
        }

        if ($this->verbose) {
            $this->writeToConsole("\n\nStats:\n");
            $this->writeToConsole("\nCars set to " . self::OPERATIVE_STATUS . ": " . count($carsToOperative) . "\n");
            foreach ($carsToOperative as $key => $value) {
                $this->writeToConsole("Plate: " . $value . "\n");
            }
            $this->writeToConsole("\nCars set to " . self::NON_OPERATIVE_STATUS . ": " . count($carsToMaintenance) . "\n");
            foreach ($carsToMaintenance as $key => $value) {
                $this->writeToConsole("Plate: " . $value . "\n");
            }
        }

        $this->writeToConsole(date('ymd-His').";INF;checkAlarmsAction;end;\n");
    }

    /**
     * @param integer
     * @param Cars
     */
    private function sendAlarmCommand($alarmCode, $car) {
        $strLog = sprintf("%s;INF;sendAlarmCommand;plate=%s;code=%s\n",
            date('ymd-His'),
            $car->getPlate(),
            $alarmCode);
        $this->writeToConsole($strLog);

        //$this->writeToConsole("Alarm code = " . $alarmCode . "\n");

        // get all active reservations for car
        $reservations = $this->reservationsService->getActiveReservationsByCar($car->getPlate());
        //$this->writeToConsole("reservations retrieved\n");

        // car should not have active reservations
        if ($alarmCode == self::OPERATIVE_ACTION) {
            // remove current active reservation
            foreach ($reservations as $reservation) {
                $reservation->setActive(false)->setToSend(true);

                $strLog = sprintf("%s;INF;sendAlarmCommand;plate=%s;set reservation.active to false\n",
                    date('ymd-His'),
                    $car->getPlate());
                $this->writeToConsole($strLog);

                //$this->writeToConsole("set reservation.active to false\n");

                $this->entityManager->persist($reservation);
                //$this->writeToConsole("Entity manager: reservation persisted\n");
            }
            // car should have maintainers reservation
        } elseif ($alarmCode == self::MAINTENANCE_ACTION) {
            // car does not have active reservations
            if (count($reservations) == 0) {
                //$this->writeToConsole("no reservation found, creating...\n");
                // create reservation for all maintainers
                $cardsArray = [];
                $maintainersCardCodes = $this->customerService->getListMaintainersCards();
                //$this->writeToConsole("cards retrieved\n");
                // create single json string with all maintainer's card codes
                foreach ($maintainersCardCodes as $cardCode) {
                    array_push($cardsArray, $cardCode['1']);
                }
                $cardsString = json_encode($cardsArray);
                // create maintainers reservation
                $reservation = Reservations::createMaintenanceReservation($car, $cardsString);
                //$this->writeToConsole("reservation created\n");

                $this->entityManager->persist($reservation);
                //$this->writeToConsole("Entity manager: reservation persisted\n");
                $strLog = sprintf("%s;INF;sendAlarmCommand;plate=%s;reservation created\n",
                    date('ymd-His'),
                    $car->getPlate());
            } else {
                //$this->writeToConsole("reservation found, skipping creation...\n");
                $strLog = sprintf("%s;INF;sendAlarmCommand;plate=%s;reservation found, skipping creation\n",
                    date('ymd-His'),
                    $car->getPlate());
            }
            $this->writeToConsole($strLog);
        }
    }

    public function archiveReservationsAction() {
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run');
        $this->verbose = $request->getParam('verbose') || $request->getParam('v');
        $reservationsDeleted = ['USED' => [], 'DELETED' => [], 'EXPIRED' => [], 'DEACTIVATED' => [], 'ALARM-OFF' => []];
        $reservationsArchived = [];

        $this->writeToConsole("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // get reservations to delete
        $reservations = $this->reservationsService->getReservationsToDelete();
        $this->writeToConsole("Retrieved reservations to delete: " . count($reservations) . "\n\n");

        foreach ($reservations as $reservation) {
            // output reservation info
            if ($this->verbose) {
                $this->writeToConsole("Reservation id: " . $reservation->getId());
                $this->writeToConsole(" consumed_ts: " . (($reservation->getConsumedTs() == null) ? "null" : $reservation->getConsumedTs()->format('Y-m-d H:i:s')));
                $this->writeToConsole(" active: " . (($reservation->getActive()) ? 'true' : 'false'));
                $this->writeToConsole(" to_send: " . (($reservation->getToSend()) ? 'true' : 'false'));
                $this->writeToConsole(" beginning_ts: " . $reservation->getBeginningTs()->format('Y-m-d H:i:s'));
                $this->writeToConsole(" length: " . $reservation->getLength() . "\n");
            }

            // retrieve reason
            if ($reservation->getConsumedTs() != null) {
                $reason = 'USED';
            } elseif ($reservation->getDeletedTs() != null) {
                $reason = 'DELETED';
            } elseif ($reservation->getActive()) {
                // deactivate reservation and send it to car
                $this->writeToConsole("Expired reservation found. Deactivating...\n");
                $reservation->setActive(false);
                $reservation->setToSend(true);
                $this->writeToConsole("Reservation deactivated\n");
                $this->entityManager->persist($reservation);
                $this->writeToConsole("EntityManager: reservation persisted\n");
                array_push($reservationsDeleted['DEACTIVATED'], $reservation->getId());
                continue;
            } elseif ($reservation->getLength() == -1) {
                $reason = 'ALARM-OFF';
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
            $this->writeToConsole("DEACTIVATED: " . count($reservationsDeleted['DEACTIVATED']) . "\n");
            $this->writeToConsole("ALARM-OFF: " . count($reservationsDeleted['ALARM-OFF']) . "\n");
            $this->writeToConsole("Archived: " . count($reservationsArchived) . "\n\n");
        }

        $this->writeToConsole("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    private function writeToConsole($string) {
        if ($this->verbose) {
            fwrite(STDOUT, $string);
        }
    }

    public function invoiceRegistrationsAction() {
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $this->verbose = $request->getParam('verbose') || $request->getParam('v');
        $this->writeToConsole("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
        $invoicesCreated = 0;

        $this->entityManager->beginTransaction();

        try {
            // get customers with first payment completed and no invoice
            $customers = $this->customerService->getCustomersFirstPaymentCompletedNoInvoice();
            $this->writeToConsole("Retrieved customers: " . count($customers) . "\n\n");

            foreach ($customers as $customer) {
                $this->writeToConsole('Customer: ' . $customer->getId() . "\n");
                $this->writeToConsole("Invoice not found\n");
                $invoice = $this->invoicesService->prepareInvoiceForFirstPayment($customer);
                $this->entityManager->persist($invoice);
                $this->writeToConsole("EntityManager: invoice persisted\n\n");
                $invoicesCreated ++;
            }

            // save invoices to db
            $this->writeToConsole("EntityManager: about to flush\n");
            $this->entityManager->flush();
            $this->writeToConsole("EntityManager: flushed\n");

            if (!$dryRun) {
                $this->entityManager->commit();
                $this->writeToConsole("Created " . $invoicesCreated . " invoices\n\n");
            } else {
                $this->entityManager->rollback();
            }

            $this->writeToConsole("\nDone\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
        } catch (\Exception $e) {
            $this->entityManager->rollback();

            $this->writeToConsole("Exception message: " . $e->getMessage());
        }
    }

    public function closeOldTripMaintainerAction() {
        $this->verbose = true;
        $tripsClose = array();

        $this->writeToConsole(date_create()->format('y-m-d H:i:s') . ";INF;closeOldTripMaintainerAction;start\n");

        //Found the trips open more that 24 h
        $tripsOpen24 = $this->tripsService->findTripsForCloseOldTripMaintainer("-24 hour", null, "AND (ca.keyStatus='OFF' OR (ca.keyStatus='ON' AND ca.parking=true)) ");
        $this->writeToConsole(date_create()->format('y-m-d H:i:s') . ";INF;closeOldTripMaintainerAction;count(tripsOpen24);" . count($tripsOpen24) . "\n");

        foreach ($tripsOpen24 as $trip) {
            if (!in_array($trip, $tripsClose)) {
                $this->writeToConsole(date_create()->format('y-m-d H:i:s') . ";INF;closeOldTripMaintainerAction;close;tripsOpen24=" . $trip->getId() . "\n");
                array_push($tripsClose, $trip);

                $this->tripsService->closeTripParam($trip, false, null, true, true);
            }
        }

        //Found the trip duplicated
        $tripsOpenMaintainer = $this->tripsService->findTripsForCloseOldTripMaintainer(null, null, null);
        $this->writeToConsole(date_create()->format('y-m-d H:i:s') . ";INF;closeOldTripMaintainerAction;count(tripsOpenMaintainer);" . count($tripsOpenMaintainer) . "\n");
        foreach ($tripsOpenMaintainer as $trip) {
            //$this->writeToConsole(date_create()->format('y-m-d H:i:s').";INF;closeOldTripMaintainerAction;carPlate;".$trip->getCar()->getPlate()."\n");

            $openTrips = $this->tripsService->getTripsOpenByCar($trip->getCar());
            //$this->writeToConsole(date_create()->format('y-m-d H:i:s').";INF;closeOldTripMaintainerAction;trip=".$trip->getId().";count(openTrips)=".count($openTrips)."\n");

            for ($i = 0; $i < count($openTrips) - 1; $i++) { // loop until the last
                if (in_array($openTrips[$i], $tripsOpenMaintainer)) { // it's a trip open from maintainer
                    if (!in_array($openTrips[$i], $tripsClose)) {
                        $this->writeToConsole(date_create()->format('y-m-d H:i:s') . ";INF;closeOldTripMaintainerAction;close;tripsDuplicate=" . $openTrips[$i]->getId() . "\n");
                        array_push($tripsClose, $openTrips[$i]);
                        $this->tripsService->closeTripParam($openTrips[$i], false, null, false, true);
                    }
                }
            }
        }

        $this->writeToConsole(date_create()->format('y-m-d H:i:s') . ";INF;closeOldTripMaintainerAction;stop\n");
    }

    public function alertCreditCardExpirationAction() {

        $this->prepareLogger();
        $format = "%s;INF;alertCreditCardExpirationAction;strat\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

        $request = $this->getRequest();
        $this->avoidEmails = $request->getParam('no-emails') || $request->getParam('e');
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');

        $format = "%s;INF;alertCreditCardExpirationAction;";
        if(!$this->avoidEmails){
            $format .= "SendEmails = TRUE;";
        } else {
            $format .= "SendEmails = FALSE;";
        }

        if (!$dryRun) {
            $format .= "DryRun = TRUE;";
        } else {
            $format .= "DryRun = FALSE;";
        }

        $format .= "\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

        $pan_expiry = '20' . date_create()->format('ym');

        $customersid = $this->cartasiContractsService->getCustomersContractExpiring($pan_expiry);

        foreach ($customersid as $customerId) {
            $customer = $this->customerService->findById($customerId);

            $contract = $this->cartasiContractsService->getCartasiContract($customer);
            if (count($contract) > 0) {
                if ($contract->getPanExpiry() == $pan_expiry){
                    if (!$this->avoidEmails) {
                        if (!$dryRun) {
                            $this->sendEmail($customer->getEmail(), $customer->getName(), $customer->getLanguage(), 20);
                        }
                    }
                    $format = "%s;INF;alertCreditCardExpirationAction;Customer_id = %d;Email = %s\n";
                    $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s'), $customer->getId(), $customer->getEmail()));
                }
            }
            $this->customerService->clearAllEntityManager();
        }

        $format = "%s;INF;alertCreditCardExpirationAction;end\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
    }

    public function disableCreditCardAction() {
        $this->prepareLogger();
        $format = "%s;INF;disableCreditCardAction;strat\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

        $request = $this->getRequest();
        $this->avoidEmails = $request->getParam('no-emails') || $request->getParam('e');
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $paramDate = $request->getParam('date');

        $format = "%s;INF;disableCreditCardAction;";
        if(!$this->avoidEmails) {
            $format .= "SendEmails = TRUE;";
        } else {
            $format .= "SendEmails = FALSE;";
        }

        if (!$dryRun){
            $format .= "DryRun = TRUE;";
        } else {
            $format .= "DryRun = FALSE;";
        }

        if (!is_null($paramDate)) {
            $pan_expiry = $paramDate;
        } else {
            $pan_expiry = '20' . date_create('first day of last month')->format('ym');
        }

        $format .= "PanExpiry = " . $pan_expiry . ";";

        $format .= "\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));

        $customersId = $this->cartasiContractsService->getCustomersContractExpiring($pan_expiry);
        foreach ($customersId as $customerId) {
            $customer = $this->customerService->findById($customerId);
            $contract = $this->cartasiContractsService->getCartasiContract($customer);
            if (count($contract) > 0) {
                if ($contract->getPanExpiry() == $pan_expiry) {
                    if (!$dryRun) {
                        $this->customerDeactivationService->deactivateByScriptDisableCreditCard($customer);
                        $this->cartasiContractsService->disableContract($contract);
                    }
                    $format = "%s;INF;disableCreditCardAction;Customer_id = %d;Contract_id = %d\n";
                    $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s'), $customer->getId(), $contract->getId()));
                    if (!$this->avoidEmails) {
                        if (!$dryRun) {
                            $this->sendEmail($customer->getEmail(), $customer->getName(), $customer->getLanguage(), 21);
                        }
                    }
                }
            }
            $this->customerService->clearAllEntityManager();
        }

        $format = "%s;INF;disableCreditCardAction;end\n";
        $this->logger->log(sprintf($format, date_create()->format('y-m-d H:i:s')));
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

    private function prepareLogger() {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);
    }

    /**
     * Check the condition by fleet_id and bettery lever, for ooo.
     * 
     * @param \SharengoCore\Entity\Cars $car
     * @return boolean
     */
    private function checkBatteryConditions(\SharengoCore\Entity\Cars $car) {
        $result = FALSE;

        if(!is_null($this->batterySpecific)) {
            foreach($this->batterySpecific as $condition){
                if(isset($condition["fleet_id"]) && isset($condition["battery"])) {
                    if ($car->getFleet()->getId() == $condition["fleet_id"]) {
                         if ($car->getBattery() < $condition["battery"]) {
                             return TRUE;
                         } else {
                             return FALSE;
                         }
                    }
                }
            }
        }

        if ($car->getBattery() < $this->battery) {
            $result = TRUE;
        }

        return $result;
    }

    /**
     * Parse the comuni.json file that come from https://github.com/matteocontrini/comuni-json
     *
     * To update, run the follow command:
     *
     * php composer.phar update matteocontrini/comuni-json
     * php public/index.php municipality update
     *
     */
    public function municipalityUpdateAction() {

        $municipalityJsonFile = file_get_contents("vendor/matteocontrini/comuni-json/comuni.json");
        $municipalityArray = json_decode($municipalityJsonFile);
        $this->logger->log(sprintf("%s;INF;municipalityUpdateAction;start;%d\n", date_create()->format('y-m-d H:i:s'), count($municipalityArray)));

        foreach($municipalityArray as $municipality){
            $sql = sprintf("UPDATE italian_municipalities SET zip_codes='%s' WHERE cadastral_code='%s'", json_encode($municipality->cap), $municipality->codiceCatastale);
            $stmt = $this->entityManager->getConnection()->prepare($sql);
            $stmt->execute();
            //var_dump($sql);
        }

        $this->logger->log(sprintf("%s;INF;municipalityUpdateAction;end\n", date_create()->format('y-m-d H:i:s')));
    }
}
