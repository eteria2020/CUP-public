<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    public function loginAction()
    {
        return new ViewModel();
    }

    public function signupAction()
    {
        return new ViewModel();
    }
}
