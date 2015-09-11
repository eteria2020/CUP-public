<?php

namespace Application\Controller;

use SharengoCore\Entity\Queries\FirstPaymentInvoicesVersionTwo;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FixInvoicesBodyControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $invoicesQuery = new FirstPaymentInvoicesVersionTwo($entityManager);

        return new FixInvoicesBodyController($invoicesQuery, $entityManager);
    }
}
