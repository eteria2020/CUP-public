<?php

namespace Application\Authentication\Adapter;

use Zend\Authentication\Result as AuthenticationResult;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcUser\Authentication\Adapter\AdapterChainEvent as AuthEvent;
use ZfcUser\Authentication\Adapter\AbstractAdapter;

class Sharengo extends AbstractAdapter implements ServiceManagerAwareInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @param AuthEvent $e
     * @return bool|void
     */
    public function authenticate(AuthEvent $e)
    {
        if ($this->isSatisfied()) {
            $storage = $this->getStorage()->read();
            $e->setIdentity($storage['identity'])
                ->setCode(AuthenticationResult::SUCCESS)
                ->setMessages(['Authentication successful.']);
            return;
        }

        $identity   = $e->getRequest()->getPost()->get('identity');
        $credential = hash("MD5", $e->getRequest()->getPost()->get('credential'));

        $userObject = $this->getCustomersService()->getUserByEmailPassword($identity, $credential);

        if (!$userObject) {
            $e->setCode(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND)
                ->setMessages(['A record with the supplied identity could not be found.']);
            $this->setSatisfied(false);
            return false;
        }

        // Success!
        $e->setIdentity($userObject->getId());
        $this->setSatisfied(true);
        $storage = $this->getStorage()->read();
        $storage['identity'] = $e->getIdentity();
        $this->getStorage()->write($storage);
        $e->setCode(AuthenticationResult::SUCCESS)
            ->setMessages(['Authentication successful.']);
    }

    /**
     * @return SharengoCore\Service\CustomersService
     */
    public function getCustomersService()
    {
        return $this->getServiceManager()->get('SharengoCore\Service\CustomersService');
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
}
