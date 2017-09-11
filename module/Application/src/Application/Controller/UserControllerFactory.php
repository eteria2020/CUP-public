<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use SharengoCore\Service\EmailService;
use SharengoCore\Service\FleetService;

class UserControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form1 = $serviceLocator->getServiceLocator()->get('RegistrationForm');
        $form2 = $serviceLocator->getServiceLocator()->get('RegistrationForm2');
        $registrationService = $serviceLocator->getServiceLocator()->get('RegistrationService');
        $customerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $languageService = $serviceLocator->getServiceLocator()->get('LanguageService');
        $profilingPlatformService =  $serviceLocator->getServiceLocator()->get('ProfilingPlatformService');
        $translationService = $serviceLocator->getServiceLocator()->get('Translator');
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        //$sharedLocator = $serviceLocator->getServiceLocator();
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $smsConfig = $config['sms'];
        $emailService = $serviceLocator->getServiceLocator()->get('\SharengoCore\Service\EmailService');
        $fleetService = $serviceLocator->getServiceLocator()->get('\SharengoCore\Service\FleetService');
        $tripService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');
        
        return new UserController(
            $form1,
            $form2,
            $registrationService,
            $customerService,
            $languageService,
            $profilingPlatformService,
            $translationService,
            $hydrator,
            $config['sms'],
            $emailService,
            $fleetService,
            $tripService
        );
    }
}
