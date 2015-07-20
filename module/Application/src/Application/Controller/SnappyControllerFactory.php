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

        return new SnappyController($viewRenderer, $pdfService);
    }
}
