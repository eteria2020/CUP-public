<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class RegistrationFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Application\Form\RegistrationForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $countriesService = $serviceLocator->get('SharengoCore\Service\CountriesService');
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $provincesService = $serviceLocator->get('SharengoCore\Service\ProvincesService');
        $promoCodeService = $serviceLocator->get('SharengoCore\Service\PromoCodesService');
        $fleetService = $serviceLocator->get('SharengoCore\Service\FleetService');
        $userFieldset = new UserFieldset(
            $translator,
            $hydrator,
            $countriesService,
            $customersService,
            $provincesService,
            $fleetService
        );
        $promoCodeFieldset = new PromoCodeFieldset($translator, $promoCodeService);

        return new RegistrationForm($translator, $userFieldset, $promoCodeFieldset);
    }
}
