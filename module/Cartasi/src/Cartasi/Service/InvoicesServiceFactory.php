<?php

namespace Cartasi\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class InvoicesServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $invoicesRepository = $entityManager->getRepository('\Cartasi\Entity\Invoices');
        $config = $serviceLocator->get('Config');
        $invoicesConfig = $config['invoice'];

        return new InvoicesService($invoicesRepository, $entityManager, $invoicesConfig);
    }
}
