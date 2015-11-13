<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class IsLoggedIn extends AbstractHelper
{
	/**
	 * @return boolean returns true if a customer is logged in, returns false otherwise
	 */
    public function __invoke()
    {
    	// get instance of AuthenticationService
        $authService = $this->getView()->getHelperPluginManager()->getServiceLocator()->get('zfcuser_auth_service');
        return $authService->hasIdentity();
    }
}
