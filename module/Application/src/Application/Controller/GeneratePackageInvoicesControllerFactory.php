<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GeneratePackageInvoicesControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedManager = $serviceLocator->getServiceLocator();

        $bonusPaymentService = $sharedManager->get('SharengoCore\Service\BonusPackagePaymentService');
        $logger = $sharedManager->get('SharengoCore\Service\SimpleLoggerService');

        return new GeneratePackageInvoicesController(
            $bonusPaymentService,
            $logger
        );
    }
}
