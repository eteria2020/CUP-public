<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ProfileFormFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ProfileForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $countriesService = $serviceLocator->get('SharengoCore\Service\CountriesService');
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $provincesService = $serviceLocator->get('SharengoCore\Service\ProvincesService');
        $userService = $serviceLocator->get('zfcuser_auth_service');
        $fleetService = $serviceLocator->get('SharengoCore\Service\FleetService');
        $customerFieldset = new CustomerFieldset(
            $translator,
            $hydrator,
            $countriesService,
            $customersService,
            $userService,
            $provincesService,
            $fleetService
        );

        return new ProfileForm($customerFieldset, $entityManager);
    }
}
