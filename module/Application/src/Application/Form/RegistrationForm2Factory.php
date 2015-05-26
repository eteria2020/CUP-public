<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class RegistrationForm2Factory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Application\Form\RegistrationForm2
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $countriesService = $serviceLocator->get('SharengoCore\Service\CountriesService');
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $authorityService = $serviceLocator->get('SharengoCore\Service\AuthorityService');
        $driverFieldset = new DriverFieldset(
            $translator,
            $hydrator,
            $countriesService,
            $customersService,
            $authorityService
        );

        return new RegistrationForm2($translator, $driverFieldset);
    }
}