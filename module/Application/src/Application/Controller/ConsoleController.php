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
        $battery,
        $delay
    ) {
        $this->customerService = $customerService;
        $this->carsService = $carsService;
        $this->reservationsService = $reservationsService;
        $this->entityManager = $entityManager;
        $this->profilingPlatformService = $profilingPlatformService;
        $this->battery = $battery;
        $this->delay = $delay;
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
        // define statuses
        $operative = 'operative';
        $maintenance = 'maintenance';

        fwrite(STDOUT, "\nStarted\ntime = " . time() . "\n\n");

        // get all cars
        $cars = $this->carsService->getListCars();
        fwrite(STDOUT, "Cars number = " . count($cars) . "\n");

        foreach ($cars as $car) {
            fwrite(STDOUT, "\nCar: plate = " . $car->getPlate());
            fwrite(STDOUT, " battery = " . $car->getBattery());
            fwrite(STDOUT, " last time = " . $car->getLastContact()->getTimestamp());
            fwrite(STDOUT, " charging = " . $car->getCharging());
            fwrite(STDOUT, "\n");

            // defines if car status should be saved
            $flagPersist = false;
            // defines if car should be in maintenance
            $isAlarm =  $car->getBattery() < $this->battery ||
                        time() - $car->getLastContact()->getTimestamp() > $this->delay * 60 ||
                        $car->getCharging() == true;
            fwrite(STDOUT, "isAlarm = " . (($isAlarm) ? 'true' : 'false') . "\n");
            $status = $car->getStatus();
            fwrite(STDOUT, "status = " . $status . "\n");
            
            if ($status == $operative && $isAlarm) {
                $car->setStatus($maintenance);
                $flagPersist = $this->sendAlarmCommand(1, $car);
                fwrite(STDOUT, "status changed to " . $maintenance . "\n");

            } elseif ($status == $maintenance && !$isAlarm) {
                $car->setStatus($operative);
                $flagPersist = $this->sendAlarmCommand(0, $car);
                fwrite(STDOUT, "status changed to " . $operative . "\n");

            }

            if ($flagPersist) {
                $this->entityManager->persist($car);
                fwrite(STDOUT, "\ncar persisted\n");

            }

        }

        fwrite(STDOUT, "\nabout to flush\n");
        $this->entityManager->flush();
        fwrite(STDOUT, "flushed\n");

        fwrite(STDOUT, "\n\ndone\n\n");

    }

    /**
     * @param integer
     * @param Cars
     * @return boolean
     */
    private function sendAlarmCommand($alarmCode, $car)
    {
        fwrite(STDOUT, "Alarm code = " . $alarmCode . "\n");

        // remove current active reservation
        if($alarmCode == 0) {

            $reservations = $this->reservationsService->getActiveReservationsByCar($car->getPlate());
            fwrite(STDOUT, "reservations retrieved\n");

            if (count($reservations) == 1) {
                $reservation = $reservations[0];
                fwrite(STDOUT, "reservation retrieved\n");
                $reservation->setActive(false);
                $this->entityManager->persist($reservation);
                fwrite(STDOUT, "reservation persisted\n");
                return true;
            } else {
                fwrite(STDOUT, "multiple reservations retrieved: " . count($reservations) . "\n");
                return false;
            }

        }
        // create reservation for all maintainers
        elseif ($alarmCode == 1) {

            $cardsString = '';
            $maintainersCards = $this->customerService->getListMaintainersCards();
            fwrite(STDOUT, "cards retrieved\n");
            // create single string with all maintainer's cards code
            foreach ($maintainersCards as $card) {
                fwrite(STDOUT, "card code = " . $card->getCode() . " added\n");
                $cardsString .= $card->getCode() . ','; // TODO - how are they written to string?
            }

            $reservation = new Reservations();
            fwrite(STDOUT, "reservation created\n");

            $reservation->setTs(date_create());
            $reservation->setCar($car);
            $reservation->setCustomer();
            $reservation->setBeginningTs(date_create());
            $reservation->setActive(true);
            $reservation->setLength(-1);
            $reservation->setToSend(true);
            $reservation->setCards($cardsString);

            $this->entityManager->persist($reservation);
            fwrite(STDOUT, "reservation persisted\n");

        }

        return true;

    }
    
}
