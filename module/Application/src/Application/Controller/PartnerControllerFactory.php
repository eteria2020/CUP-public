<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PartnerControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $partnerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PartnerService');
        $provincesRepository = $entityManager->getRepository('\SharengoCore\Entity\Provinces');
        $userEventsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\UserEventsService');
        $telepassPayService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TelepassPayService');     //TODO: only for test
        $tripPaymentsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripPaymentsService');   //TODO: only for test

        return new PartnerController(
            $partnerService,
            $provincesRepository,
            $userEventsService,
            $telepassPayService,
            $tripPaymentsService
        );
    }
}
