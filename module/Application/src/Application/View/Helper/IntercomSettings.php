<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class IntercomSettings extends AbstractHelper
{
    protected $intercomKey;
    protected $loggedUser;

    public function __construct($intercomKey, $loggedUser) {
        $this->$intercomKey = $intercomKey;
        $this->$loggedUser = $loggedUser;
    }

    public function __invoke()
    {
        $ret = "";
        if ($this->$intercomKey) {
            $ret = "var intercomAppId='".$this->$intercomKey."';";
            if($this->$loggedUser) {
                 $ret .= "var intercomCustomerEmail='".$loggedUser->getEmail()."';";
                 $ret .= "var intercomCustomerId='".$loggedUser->getId()."';";
             }
        }

        return $ret;
    }
}