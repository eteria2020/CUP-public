<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CarsService;
use SharengoCore\Service\ReservationsService;
use SharengoCore\Entity\Reservations;
use Doctrine\ORM\EntityManager;
use Application\Service\ProfilingPlaformService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ConsoleController extends AbstractActionController
{


    const OPERATIVE = 'operative';

    const MAINTENANCE = 'maintenance';

    const OPERATIVEACTION = 0;

    const MAINTENANCEACTION = 1;

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

        fwrite(STDOUT, "\nStarted\ntime = " . time() . "\n\n");

        // get all cars
        $cars = $this->carsService->getListCars();
        fwrite(STDOUT, "Cars number = " . count($cars) . "\n");

        foreach ($cars as $car) {
            fwrite(STDOUT, "\nCar: plate = " . $car->getPlate());
            fwrite(STDOUT, " battery = " . $car->getBattery());
            fwrite(STDOUT, " last time = " . $car->getLastContact()->getTimestamp());
            fwrite(STDOUT, " charging = " . (($car->getCharging()) ? 'true' : 'false'));
            fwrite(STDOUT, "\n");

            // defines if car status should be saved
            $flagPersist = false;
            // defines if car should be in maintenance
            $isAlarm =  $car->getBattery() < $this->battery ||
                        time() - $car->getLastContact()->getTimestamp() > $this->delay * 60 ||
                        $car->getCharging();
            fwrite(STDOUT, "isAlarm = " . (($isAlarm) ? 'true' : 'false') . "\n");
            $status = $car->getStatus();
            fwrite(STDOUT, "status = " . $status . "\n");
            
            if ($status == self::OPERATIVE && $isAlarm) {
                $car->setStatus(self::MAINTENANCE);
                $this->sendAlarmCommand(self::MAINTENANCEACTION, $car);
                $flagPersist = true;
                fwrite(STDOUT, "status changed to " . self::MAINTENANCE . "\n");

            } elseif ($status == self::MAINTENANCE && !$isAlarm) {
                $car->setStatus(self::OPERATIVE);
                $this->sendAlarmCommand(self::OPERATIVEACTION, $car);
                $flagPersist = true;
                fwrite(STDOUT, "status changed to " . self::OPERATIVE . "\n");

            }

            if ($flagPersist) {
                $this->entityManager->persist($car);
                fwrite(STDOUT, "\nEntity manager: car persisted\n");

            }

        }

        if (!$dryRun) {
            fwrite(STDOUT, "\nEntity manager: about to flush\n");
            $this->entityManager->flush();
            fwrite(STDOUT, "Entity manager: flushed\n");
        }

        fwrite(STDOUT, "\n\ndone\n\n");

    }

    /**
     * @param integer
     * @param Cars
     */
    private function sendAlarmCommand($alarmCode, $car)
    {
        fwrite(STDOUT, "Alarm code = " . $alarmCode . "\n");

        // remove current active reservation
        if($alarmCode == self::OPERATIVEACTION) {

            $reservations = $this->reservationsService->getActiveReservationsByCar($car->getPlate());
            fwrite(STDOUT, "reservations retrieved\n");

            foreach ($reservations as $reservation) {
                $reservation->setActive(false)
                    ->setToSend(true);
                fwrite(STDOUT, "set reservation.active to false\n");
                $this->entityManager->persist($reservation);
                fwrite(STDOUT, "Entity manager: reservation persisted\n");
            }
            return;
        }
        // create reservation for all maintainers
        elseif ($alarmCode == self::MAINTENANCEACTION) {

            $cardsArray = [];
            $maintainersCardCodes = $this->customerService->getListMaintainersCards();
            fwrite(STDOUT, "cards retrieved\n");
            // create single json string with all maintainer's card codes
            foreach ($maintainersCardCodes as $cardCode) {
                fwrite(STDOUT, "card code = " . $cardCode['1'] . " added\n");
                array_push($cardsArray, $cardCode['1']);
            }
            $cardsString = json_encode($cardsArray);

            $reservation = new Reservations();
            fwrite(STDOUT, "reservation created\n");

            $reservation->setTs(date_create())
                ->setCar($car)
                ->setCustomer(null)
                ->setBeginningTs(date_create())
                ->setActive(true)
                ->setLength(-1)
                ->setToSend(true)
                ->setCards($cardsString);

            $this->entityManager->persist($reservation);
            fwrite(STDOUT, "Entity manager: reservation persisted\n");

        }

    }
    
}
