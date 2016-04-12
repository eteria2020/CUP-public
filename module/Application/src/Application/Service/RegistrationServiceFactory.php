<?php

namespace Application\Service;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Mail\Transport\Sendmail;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegistrationServiceFactory implements FactoryInterface
{
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
        $deactivationService = $serviceLocator->get('SharengoCore\Service\CustomerDeactivationService');
        $events = $serviceLocator->get('EventManager');
        $events->addIdentifiers('Application\Service\RegistrationService');

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
            $subscriptionBonus,
            $deactivationService,
            $events
        );
    }
}
