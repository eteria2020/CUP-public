<?php

namespace Cartasi\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TransactionsServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // Dependencies are fetched from Service Manager
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $transactionsRepository = $entityManager->getRepository('\Cartasi\Entity\Transactions');

        return new TransactionsService($transactionsRepository);
    }
}
