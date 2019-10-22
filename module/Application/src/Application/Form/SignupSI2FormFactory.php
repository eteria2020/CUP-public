<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class SignupSI2FormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return NewRegistrationForm2|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $countriesService = $serviceLocator->get('SharengoCore\Service\CountriesService');
        $provincesService = $serviceLocator->get('SharengoCore\Service\ProvincesService');
        $config = $serviceLocator->get('Config');
        $serverInstance = isset($config["serverInstance"]["id"]) ? $config["serverInstance"]["id"] : null;

        $signupSI2Fieldset = new SignupSI2Fieldset( $translator, $hydrator, $customersService, $countriesService, $provincesService, $serverInstance);

        return new SignupSI2Form($translator, $signupSI2Fieldset, $entityManager, $serverInstance);
    }
}