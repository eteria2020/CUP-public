<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form1 = $serviceLocator->getServiceLocator()->get('RegistrationForm');
        $form2 = $serviceLocator->getServiceLocator()->get('RegistrationForm2');
        $registrationService = $serviceLocator->getServiceLocator()->get('RegistrationService');
        $languageService = $serviceLocator->getServiceLocator()->get('LanguageService');

        return new UserController($form1, $form2, $registrationService, $languageService);
    }
}
