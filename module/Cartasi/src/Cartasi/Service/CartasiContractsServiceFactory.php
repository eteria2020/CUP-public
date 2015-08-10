<?php

namespace Cartasi\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CartasiContractsServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $contractsRepository = $entityManager->getRepository('Cartasi\Entity\Contracts');

        return new CartasiContractsService(
            $contractsRepository
        );
    }
}
