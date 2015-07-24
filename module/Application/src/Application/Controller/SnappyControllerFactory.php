<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SnappyControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $viewRenderer = $serviceLocator->get('view_manager')->getRenderer();
        $pdfService = $serviceLocator->get('mvlabssnappy.pdf.service');
        $invoiceService = $serviceLocator->get('SharengoCore\Service\Invoices');
        $authService = $serviceLocator->get('zfcuser_auth_service');

        return new SnappyController($viewRenderer, $pdfService, $invoiceService, $authService);
    }
}
