<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GenerateExtraInvoicesControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $extraPaymentsService = $sharedServiceManager->get('SharengoCore\Service\ExtraPaymentsService');
        $extraPaymentsSearchService = $sharedServiceManager->get('SharengoCore\Service\ExtraPaymentsSearchService');
        $logger = $sharedServiceManager->get('SharengoCore\Service\SimpleLoggerService');

        return new GenerateExtraInvoicesController(
            $extraPaymentsService,
            $extraPaymentsSearchService,
            $logger
        );
    }
}
