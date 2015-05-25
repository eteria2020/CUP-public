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
}
