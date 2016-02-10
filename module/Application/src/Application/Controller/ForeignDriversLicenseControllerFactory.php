<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForeignDriversLicenseControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $form = $sharedLocator->get('ForeignDriversLicenseForm');
        $customersService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $authorityService = $sharedLocator->get('SharengoCore\Service\AuthorityService');
        $foreignDriversLicenseService = $sharedLocator->get('SharengoCore\Service\ForeignDriversLicenseService');

        return new ForeignDriversLicenseController(
            $form,
            $customersService,
            $authorityService,
            $foreignDriversLicenseService
        );
    }
}
