<?php

namespace Application\Controller;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CustomerDeactivationService;

use Zend\Mvc\Controller\AbstractActionController;

class DisableCustomerController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var CustomerDeactivationService
     */
    private $customerDeactivationService;

    public function __construct(
        CustomersService $customersService,
        CustomerDeactivationService $customerDeactivationService
    ) {
        $this->customersService = $customersService;
        $this->customerDeactivationService = $customerDeactivationService;
    }

    public function invalidDriversLicenseAction()
    {
        $customerId = $this->params('customerId');
        if (!is_numeric($customerId)) {
            fwrite(STDOUT, 'You need to provide a valid numeric id to retrieve the customer'.PHP_EOL);
            exit;
        }

        $customer = $this->customersService->findById($customerId);
        if (!$customer instanceof Customers) {
            fwrite(STDOUT, 'The id you provided is not associated to any customer'.PHP_EOL);
            exit;
        }

        $this->customerDeactivationService->deactivateForDriversLicense($customer);

        fwrite(STDOUT, 'The customer '.$customer->getName().' '.$customer->getSurname().' was disabled correctly'.PHP_EOL);
    }
}
