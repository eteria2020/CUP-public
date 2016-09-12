<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsolePromoCodesOnceComputeFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $logger = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\SimpleLoggerService');

        $repository = $entityManager->getRepository('SharengoCore\Entity\PromoCodesOnce');
        $pciRepository = $entityManager->getRepository('SharengoCore\Entity\PromoCodesInfo');
                
        return new ConsolePromoCodesOnceCompute(
            $entityManager,
            $repository,
            $pciRepository,
            $logger);
    }
}
