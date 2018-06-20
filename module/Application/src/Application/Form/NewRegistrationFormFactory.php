<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class NewRegistrationFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Application\Form\RegistrationForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $fleetService = $serviceLocator->get('SharengoCore\Service\FleetService');
        $userFieldset = new NewUserFieldset(
            $translator,
            $hydrator,
            $customersService,
            $fleetService
        );

        return new NewRegistrationForm($translator, $userFieldset);
    }
}
