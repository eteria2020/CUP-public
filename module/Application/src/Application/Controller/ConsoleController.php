<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ConsoleController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customerService;

    public function __construct(CustomersService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function getDiscountsAction()
    {
        $customers = $this->customerService->getListCustomers();

        foreach ($customers as $customer) {
            $discount = 0;
            $this->customerService->setCustomerDiscountRate($customer, $discount);

            echo "customer done: ".$customer->getId()."\n";
        }

        echo "done\n";
    }
}
