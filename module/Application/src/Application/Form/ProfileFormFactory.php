<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ProfileFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Application\Form\ProfileForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $countriesService = $serviceLocator->get('SharengoCore\Service\CountriesService');
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $userService = $serviceLocator->get('zfcuser_auth_service');
        $customerFieldset = new CustomerFieldset(
            $translator,
            $hydrator,
            $countriesService,
            $customersService,
            $userService
        );

        return new ProfileForm($customerFieldset, $entityManager);
    }
}
