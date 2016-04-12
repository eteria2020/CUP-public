<?php

namespace Application\Listener;

use Application\Service\ProviderAuthenticationService;
use SharengoCore\Service\ProviderAuthenticatedCustomersService;
use SharengoCore\Exception\ProviderAuthenticatedCustomerNotFoundException;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\Session\Container;

final class ProviderAuthenticatedCustomerRegistered implements SharedListenerAggregateInterface
{
    /**
     * @var ProviderAuthenticatedCustomersService
     */
    private $providerAuthenticatedCustomersService;

    public function __construct(
        ProviderAuthenticatedCustomersService $providerAuthenticatedCustomersService
    ) {
        $this->providerAuthenticatedCustomersService = $providerAuthenticatedCustomersService;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'Application\Service\RegistrationService',
            'registeredCustomerPersisted',
            [$this, 'linkProviderAuthenticatedCustomer']
        );
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($index, $callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function linkProviderAuthenticatedCustomer(EventInterface $e)
    {
        $customer = $e->getParam('customer');

        // try to retrieve provider authenticated customer from session
        $container = new Container(ProviderAuthenticationService::SESSION_KEY);
        $uuid = $container->offsetGet(ProviderAuthenticationService::UUID);

        try {
            $providerAuthenticatedCustomer = $this->providerAuthenticatedCustomersService->getCustomerById($uuid);

            $this->providerAuthenticatedCustomersService->linkCustomer($providerAuthenticatedCustomer, $customer);

            $container->offsetUnset(ProviderAuthenticationService::UUID);
        } catch (ProviderAuthenticatedCustomerNotFoundException $e) {
            return;
        }
    }
}
