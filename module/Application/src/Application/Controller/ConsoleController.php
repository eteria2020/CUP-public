<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
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
     * @var ProfilingPlatformservice
     */
    private $profilingPlatformService;

    public function __construct(
        CustomersService $customerService,
        ProfilingPlaformService $profilingPlatformService
    ) {
        $this->customerService = $customerService;
        $this->profilingPlatformService = $profilingPlatformService;
    }

    public function getDiscountsAction()
    {
        $customers = $this->customerService->getListCustomers();

        foreach ($customers as $customer) {
            $email = $customer->getEmail();

            try {
                $discount = $this->profilingPlatformService->getDiscountByEmail($email);
            } catch (\Exception $e) {
                $discount = 0;
            }

            $this->customerService->setCustomerDiscountRate($customer, $discount);

            echo "customer done: ".$email."\n";
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
    
}
