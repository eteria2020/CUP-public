<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;

class IntercomSettings extends AbstractHelper
{
    protected $intercomKey;
    protected $authenticationService;
    protected $loggedUser;

    public function __construct($intercomKey, AuthenticationService $authenticationService) {
        $this->intercomKey = $intercomKey;
        $this->authenticationService = $authenticationService;
        //$this->$loggedUser = "";
        
    }

    public function __invoke()
    {
        $ret = "";
        if ($this->intercomKey) {
            $ret = sprintf("var intercomAppId='%s';", $this->intercomKey);
            if($this->authenticationService->hasIdentity()) {
                $user = $this->authenticationService->getIdentity();
                $ret .= sprintf("var intercomCustomerEmail='%s';", $user->getEmail());
                $ret .= sprintf("var intercomCustomerId='%s';", $user->getId());
             } else 
             {
                 $ret .= "var intercomCustomerEmail='';";
             }
        }

        return $ret;
    }
}