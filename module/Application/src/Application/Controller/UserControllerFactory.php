<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use SharengoCore\Service\EmailService;
use SharengoCore\Service\FleetService;
use SharengoCore\Entity\Configurations;

class UserControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form1 = $serviceLocator->getServiceLocator()->get('RegistrationForm');
        $form2 = $serviceLocator->getServiceLocator()->get('RegistrationForm2');
        $newForm = $serviceLocator->getServiceLocator()->get('NewRegistrationForm');
        $newForm2 = $serviceLocator->getServiceLocator()->get('NewRegistrationForm2');
        $optionalForm = $serviceLocator->getServiceLocator()->get('OptionalRegistrationForm');
        $registrationService = $serviceLocator->getServiceLocator()->get('RegistrationService');
        $customerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $customerNoteService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomerNoteService');
        $usersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\usersService');
        $promoCodeService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PromoCodesService');
        $promoCodesOnceService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PromoCodesOnceService');
        $promoCodesMemberGetMemberService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PromoCodesMemberGetMemberService');
        $languageService = $serviceLocator->getServiceLocator()->get('LanguageService');
        $profilingPlatformService =  $serviceLocator->getServiceLocator()->get('ProfilingPlatformService');
        $translationService = $serviceLocator->getServiceLocator()->get('Translator');
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        //$sharedLocator = $serviceLocator->getServiceLocator();
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $emailService = $serviceLocator->getServiceLocator()->get('\SharengoCore\Service\EmailService');
        $fleetService = $serviceLocator->getServiceLocator()->get('\SharengoCore\Service\FleetService');
        $tripService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');
        $foreignDriversLicenseService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ForeignDriversLicenseService');
        $promoCodeACIService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PromoCodesACIService');

        $configurationService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ConfigurationsService');
        $smsConfigurations = $configurationService->getConfigurationsKeyValueBySlug(Configurations::SMS);


        return new UserController(
            $form1,
            $form2,
            $newForm,
            $newForm2,
            $optionalForm,
            $registrationService,
            $customerService,
            $customerNoteService,
            $usersService,
            $languageService,
            $profilingPlatformService,
            $translationService,
            $hydrator,
            $config['sms'],
            $emailService,
            $fleetService,
            $tripService,
            $promoCodeService,
            $promoCodesOnceService,
            $promoCodesMemberGetMemberService,
            $foreignDriversLicenseService,
            $config['googleMaps'],
            $promoCodeACIService,
            $smsConfigurations,
            $config['smsGatewayMe'],
            $config['semysms']
        );
    }
}
