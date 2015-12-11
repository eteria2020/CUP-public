<?php

namespace Application\Listener;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CustomerDeactivationService;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\EventInterface;

final class DriversLicensePostValidationListener implements SharedListenerAggregateInterface
{
    /**
     * @var CustomersService $customersService
     */
    private $customersService;

    /**
     * @var CustomerDeactivationService $customerDeactivator
     */
    private $customerDeactivator;

    public function __construct(
        CustomersService $customersService,
        CustomerDeactivationService $customerDeactivator
    ) {
        $this->customersService = $customersService;
        $this->customerDeactivator = $customerDeactivator;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'MvLabsDriversLicenseValidation\Job\ValidationJob',
            'validDriversLicense',
            [$this, 'validDriversLicense']
        );

        $this->listeners[] = $events->attach(
            'MvLabsDriversLicenseValidation\Job\ValidationJob',
            'unvalidDriversLicense',
            [$this, 'unvalidDriversLicense']
        );
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function validDriversLicense(EventInterface $e)
    {
        $args = $e->getParam('args');

        $customer = $this->customersService->findByEmail($args['email'])[0];

        $this->customerDeactivator->reactivateCustomerForDriversLicense($customer, date_create());
    }

    public function unvalidDriversLicense(EventInterface $e)
    {
        $args = $e->getParam('args');

        $customer = $this->customersService->findByEmail($args['email'])[0];

        $this->customerDeactivator->deactivateForDriversLicense($customer, date_create());
    }
}
