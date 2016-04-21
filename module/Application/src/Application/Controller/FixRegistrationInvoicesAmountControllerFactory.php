<?php

namespace Application\Controller;

use SharengoCore\Entity\Queries\WrongAmountRegistrationInvoices;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FixRegistrationInvoicesAmountControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $invoicesQuery = new WrongAmountRegistrationInvoices($entityManager);
        $invoicesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\Invoices');

        return new FixRegistrationInvoicesAmountController(
            $entityManager,
            $invoicesQuery,
            $invoicesService
        );
    }
}
