<?php

namespace Application\Service;


use Traversable;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

class PaypalRequestFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $I_services
     * @return \SpeckPaypal\Service\Request
     */
    public function createService(ServiceLocatorInterface $I_services) {

        $as_config  = $I_services->get('config');

        if ($as_config instanceof Traversable) {
            $as_config = ArrayUtils::iteratorToArray($as_config);
        }

        $I_paypalConfig = new \SpeckPaypal\Element\Config($as_config['speck-paypal-api']);

        //set up http client
        $I_client = new \Zend\Http\Client;
        $I_client->setMethod('POST');
        $I_client->setAdapter(new \Zend\Http\Client\Adapter\Curl);
        $I_paypalRequest = new \SpeckPaypal\Service\Request;
        $I_paypalRequest->setClient($I_client);
        $I_paypalRequest->setConfig($I_paypalConfig);

        return $I_paypalRequest;
    }
}
