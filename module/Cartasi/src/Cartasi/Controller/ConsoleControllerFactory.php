<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsoleControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $transactionsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TransactinsService');

        return new ConsoleController($transactionsService);
    }
}
