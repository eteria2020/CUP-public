<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

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

        return new UserController(
            $form1,
            $form2,
            $registrationService,
            $customerService,
            $languageService,
            $profilingPlatformService,
            $translationService,
            $hydrator
        );
    }
}
