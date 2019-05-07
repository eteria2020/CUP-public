<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class NewRegistrationForm2Factory implements FactoryInterface
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
        $promoCodeService = $serviceLocator->get('SharengoCore\Service\PromoCodesService');
        $promoCodeOnceService = $serviceLocator->get('SharengoCore\Service\PromoCodesOnceService');
        $promoCodeACIService = $serviceLocator->get('SharengoCore\Service\PromoCodesACIService');
        $promoCodesMemberGetMemberService = $serviceLocator->get('SharengoCore\Service\PromoCodesMemberGetMemberService');
        $promoCodeFieldset = new PromoCodeFieldset($translator, $promoCodeService, $promoCodeOnceService, null, $promoCodesMemberGetMemberService, $promoCodeACIService);
        $newUserFieldset2 = new NewUserFieldset2( $translator, $hydrator, $customersService, $countriesService, $provincesService);

        return new NewRegistrationForm2($translator,$countriesService, $provincesService, $promoCodeFieldset, $newUserFieldset2, $entityManager);
    }
}