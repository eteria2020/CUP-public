<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;


use Application\Service\RegistrationService;
use Multilanguage\Service\LanguageService;
use Application\Service\ProfilingPlaformService;
use Application\Exception\ProfilingPlatformException;
use SharengoCore\Service\CustomersService;
use SharengoCore\Entity\Customers;

class UserAreaController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
