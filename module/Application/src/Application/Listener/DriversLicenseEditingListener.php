<?php

namespace Application\Listener;

use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\CustomersService;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\EventInterface;

final class DriversLicenseEditingListener implements SharedListenerAggregateInterface
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
            'Application\Controller\UserController',
            'registrationCompleted',
            [$this, 'disableCustomer']
        );

        $this->listeners[] = $events->attach(
            'Application\Controller\UserAreaController',
            'driversLicenseEdited',
            [$this, 'disableCustomer']
        );

        $this->listeners[] = $events->attach(
            'Application\Controller\UserAreaController',
            'taxCodeEdited',
            [$this, 'disableCustomer']
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

    public function disableCustomer(EventInterface $e)
    {
        $email = $e->getParam('email');
        $customer = $this->customersService->findByEmail($email)[0];
        $this->customerDeactivator->deactivateForDriversLicense($customer, date_create());
    }
}
