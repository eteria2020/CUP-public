<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DisableCustomerControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();
        $customersService = $sharedServiceLocator->get('SharengoCore\Service\CustomersService');
        $customerDeactivationService = $sharedServiceLocator->get('SharengoCore\Service\CustomerDeactivationService');
        $customerNoteService = $sharedServiceLocator->get('SharengoCore\Service\CustomerNoteService');
        $usersService = $sharedServiceLocator->get('SharengoCore\Service\UsersService');
        $logger = $sharedServiceLocator->get('SharengoCore\Service\SimpleLoggerService');
        $emailService = $sharedServiceLocator->get('SharengoCore\Service\EmailService');
        $portaleAutomobilistaValidationService = $sharedServiceLocator->get('MvLabsDriversLicenseValidation\Service\PortaleAutomobilistaValidationService');
        $driversLicenseValidationService = $serviceLocator->get('SharengoCore\Service\DriversLicenseValidationService');

        return new DisableCustomerController(
            $customersService,
            $customerDeactivationService,
            $customerNoteService,
            $usersService,
            $logger,
            $emailService,
            $portaleAutomobilistaValidationService,
            $driversLicenseValidationService
        );
    }
}
