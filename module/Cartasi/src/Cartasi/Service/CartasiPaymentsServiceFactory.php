<?php

namespace Cartasi\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CartasiPaymentsServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $transactionsRepository = $entityManager->getRepository('Cartasi\Entity\Transactions');
        $contractsRepository = $entityManager->getRepository('Cartasi\Entity\Contracts');

        return new CartasiPaymentsService(
            $transactionsRepository,
            $contractsRepository
        );
    }
}
