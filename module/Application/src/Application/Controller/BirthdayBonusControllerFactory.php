<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BirthdayBonusControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();

        $bonusService = $serviceLocator->get('SharengoCore\Service\BonusService');
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $logger = $serviceLocator->get('SharengoCore\Service\SimpleLoggerService');
        $config = $serviceLocator->get('Configuration')['bonus']['birthday'];

        return new BirthdayBonusController(
            $bonusService,
            $customersService,
            $entityManager,
            $logger,
            $config
        );
    }
}
