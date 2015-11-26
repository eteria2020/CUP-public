<?php

namespace MvLabs\DriversLicenseValidation\Listener;

use Zend\EventManager\ListenerAggragateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\EventManagerInterface;

final class DriversLicenseValidationListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    public function __construct()
    {
        
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'registrationCompleted',
            [$this, 'validateDriversLicense']
        );
    }

    public function  validateDriversLicense()
    {

    }
}
