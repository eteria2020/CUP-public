<?php

namespace Application\Listener;

use MvLabsDriversLicenseValidation\Service\EnqueueValidationService;
use SharengoCore\Service\CustomersService;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\EventInterface;

final class DriversLicenseValidationListener implements SharedListenerAggregateInterface
{
    /**
     * @var EnqueueValidationService $enqueueValidationService
     */
    private $enqueueValidationService;

    public function __construct(
        EnqueueValidationService $enqueueValidationService,
        CustomersService $customersService
    ) {
        $this->enqueueValidationService = $enqueueValidationService;
        $this->customersService = $customersService;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'Application\Controller\UserController',
            'registrationCompleted',
            [$this, 'validateDriversLicense']
        );

        $this->listeners[] = $events->attach(
            'Application\Controller\UserAreaController',
            'driversLicenseEdited',
            [$this, 'validateDriversLicense']
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

    public function validateDriversLicense(EventInterface $e)
    {
        $data = $e->getParams();

        $customer = $this->customersService->findByEmail($data['email']);

        // we do not request the validation of the drivers license to the
        // motorizzazione civile is the customer has a foreign drivers license
        if (!$this->customersService->customerNeedsToAcceptDriversLicenseForm($customer)) {
            $this->enqueueValidationService->validateDriversLicense($data);
        }
    }
}
