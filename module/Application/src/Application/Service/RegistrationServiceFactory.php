<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport\Sendmail;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class RegistrationServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Application\Service\RegistrationService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form1 = $serviceLocator->get('RegistrationForm');
        $form2 = $serviceLocator->get('RegistrationForm2');

        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $hydrator = new DoctrineHydrator($entityManager);

        $emailSettings = $serviceLocator->get('Configuration')['emailSettings'];
        $emailService = $serviceLocator->get('SharengoCore\Service\EmailService');

        $translationService = $serviceLocator->get('Translator');

        $viewHelperManager = $serviceLocator->get('viewHelperManager');

        $promoCodesService = $serviceLocator->get('SharengoCore\Service\PromoCodesService');

        $subscriptionBonus = $serviceLocator->get('Configuration')['subscription-bonus'];

        return new RegistrationService(
            $form1,
            $form2,
            $entityManager,
            $hydrator,
            $emailSettings,
            $emailService,
            $translationService,
            $viewHelperManager,
            $promoCodesService,
            $subscriptionBonus
        );
    }
}
