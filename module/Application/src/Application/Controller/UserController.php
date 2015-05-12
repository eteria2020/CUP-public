<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;
use Application\Service\RegistrationService;
use Multilanguage\Service\LanguageService;

class UserController extends AbstractActionController
{
    /**
     * @var \Zend\Form\Form
     */
    private $form1;

    /**
     * @var \Zend\Form\Form
     */
    private $form2;

    /**
     * @var \Application\Service\RegistrationService
     */
    private $registrationService;

    /**
     * @var \Multilanguage\Service\LanguageService
     */
    private $languageService;

    public function __construct(
        Form $form1,
        Form $form2,
        RegistrationService $registrationService,
        LanguageService $languageService
    ) {
        $this->form1 = $form1;
        $this->form2 = $form2;
        $this->registrationService = $registrationService;
        $this->languageService = $languageService;
    }

    public function loginAction()
    {
        return new ViewModel();
    }

    public function signupAction()
    {
        return new ViewModel();
    }
}
