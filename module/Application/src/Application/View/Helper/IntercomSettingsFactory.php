<?php
namespace Application\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IntercomSettingsFactory implements FactoryInterface{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $intercomKey = $serviceLocator->getServiceLocator()->get('intercomKey');
        $loggedUser = $serviceLocator->getServiceLocator()->get('loggedUser');

        return new IntercomSettings($configService, $loggedUser);
    }
}
