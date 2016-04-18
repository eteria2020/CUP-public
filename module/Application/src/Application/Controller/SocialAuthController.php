<?php

namespace Application\Controller;

use Application\Service\ProviderAuthenticationService;
use Application\Exception\CustomerRefusedAuthenticationException;
use SharengoCore\Service\ProviderAuthenticatedCustomersService;
use SharengoCore\Exception\ProviderAuthenticatedCustomerNotFoundException;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SocialAuthController extends AbstractActionController
{
    /**
     * @var ProviderAuthenticationService
     */
    private $providerAuthentication;

    /**
     * @var ProviderAuthenticatedCustomersService
     */
    private $providerAuthenticatedCustomersService;

    public function __construct(
        ProviderAuthenticationService $providerAuthentication,
        ProviderAuthenticatedCustomersService $providerAuthenticatedCustomersService
    ) {
        $this->providerAuthentication = $providerAuthentication;
        $this->providerAuthenticatedCustomersService = $providerAuthenticatedCustomersService;
    }

    public function providerAuthenticateAction()
    {
        // Get the provider from the route
        $provider = $this->getEvent()->getRouteMatch()->getParam('provider');

        try {
            $providerAuthenticatedCustomer = $this->providerAuthentication->authenticateWithProvider($provider);

            $this->providerAuthentication->welcomeCustomer($providerAuthenticatedCustomer);
        } catch (CustomerRefusedAuthenticationException $e) {
            return $this->redirect()->toRoute('home');
        } catch (\Exception $e) {
            return $this->notFoundAction();
        }

        return $this->redirect()->toRoute('scn-social-auth-user/thank-you');
    }

    public function thankYouAction()
    {
        return;
    }

    public function registerAction()
    {
        $id = $this->params('id');

        try {
            $providerAuthenticatedCustomer = $this->providerAuthenticatedCustomersService->getCustomerById($id);

            $this->providerAuthentication->warmupCache($providerAuthenticatedCustomer);
        } catch (ProviderAuthenticatedCustomerNotFoundException $e) {
            return $this->notFoundAction();
        }

        $this->redirect()->toRoute('signup');
    }
}
