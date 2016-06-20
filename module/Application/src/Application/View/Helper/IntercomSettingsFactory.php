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
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $intercomKey = $config['intercom']['key'];

        $authenticationService = $serviceLocator->getServiceLocator()->get('zfcuser_auth_service');
        //$loggedUser = "";
        //print_r($authenticationService);
        return new IntercomSettings($intercomKey, $authenticationService);
    }
}
