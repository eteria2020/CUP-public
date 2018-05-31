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
     *
     * @return \Application\Form\RegistrationForm2
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $promoCodeService = $serviceLocator->get('SharengoCore\Service\PromoCodesService');
        $promoCodeOnceService = $serviceLocator->get('SharengoCore\Service\PromoCodesOnceService');
        $promoCodesMemberGetMemberService = $serviceLocator->get('SharengoCore\Service\PromoCodesMemberGetMemberService');
        $promoCodeFieldset = new PromoCodeFieldset($translator, $promoCodeService, $promoCodeOnceService, null, $promoCodesMemberGetMemberService);
        $newUserFieldset2 = new NewUserFieldset2( $translator, $hydrator, $customersService);

        return new NewRegistrationForm2($translator, $promoCodeFieldset, $newUserFieldset2, $entityManager);
    }
}