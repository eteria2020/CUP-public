<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class DriverLicenseFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DriverLicenseForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $countriesService = $serviceLocator->get('SharengoCore\Service\CountriesService');
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $authorityService = $serviceLocator->get('SharengoCore\Service\AuthorityService');
        $userService = $serviceLocator->get('zfcuser_auth_service');
        $driverFieldset = new DriverFieldset(
            $translator,
            $hydrator,
            $countriesService,
            $customersService,
            $authorityService,
            $userService->getIdentity()->getDriverLicense()
        );

        return new DriverLicenseForm($translator, $driverFieldset, $entityManager);
    }
}