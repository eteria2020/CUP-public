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
     *
     * @var SharengoCore\Service\CustomersService
     */
    private $customersService;

    /**
     * @var \Multilanguage\Service\LanguageService
     */
    private $languageService;

    /**
     *
     * @var ProfilingPlaformService
     */
    private $profilingPlatformService;

    public function __construct(
        Form $form1,
        Form $form2,
        RegistrationService $registrationService,
        CustomersService $customersService,
        LanguageService $languageService,
        ProfilingPlaformService $profilingPlatformService
    ) {
        $this->form1 = $form1;
        $this->form2 = $form2;
        $this->registrationService = $registrationService;
        $this->customersService = $customersService;
        $this->languageService = $languageService;
        $this->profilingPlatformService = $profilingPlatformService;
    }

    public function loginAction()
    {
        return new ViewModel();
    }

    public function signupAction()
    {
        //if there are data in session, we use them to populate the form
        $registeredData = $this->form1->getRegisteredData();

        if (!empty($registeredData)) {
            $this->form1->setData([
                'user' => $registeredData->toArray()
            ]);
        }

        if ($this->getRequest()->isPost()) {
            $this->form1->setData($this->getRequest()->getPost());

            if ($this->form1->isValid()) {
                return $this->proceed($this->form1);
            } else {
                return $this->signupForm($this->form1);
            }
        } else {
            return $this->signupForm($this->form1);
        }
    }

    public function signupScoreAction() {

        $email = urldecode($this->params('email'));

        $customers = $this->customersService->findByEmail($email);
        $customer = null;
        if (count($customers) > 0) {
            $customer = $customers[0];
        }

        // Proceed only if it's a new customer for Sharengo platform
        if (null == $customer) {

            // Customer exists inside profiling platform?
            try {
                
                $discount = $this->profilingPlatformService->getDiscountByEmail($email);

                // fill form data with available infos
                $customer = new Customers();
                $customer->setEmail($email);
                $this->form1->registerRawData($customer);

                return $this->redirect()->toRoute('signup');

            } catch (ProfilingPlatformException $ex) {

                // user not found
                return $this->redirect()->toRoute('signup');
                //@todo show a custom page

            }
            
        } else {
            
            //user already registered
            return $this->redirect()->toRoute('home');
            //@todo show a custom page

        }

    }

    private function proceed($form)
    {
        $form->registerData();

        return $this->redirect()->toRoute('signup-2');
    }

    public function signup2Action()
    {
        //if there are data in session, we use them to populate the form
        $registeredData = $this->form2->getRegisteredData();

        if (!empty($registeredData)) {
            $this->form2->setData([
                'driver' => $registeredData->toArray()
            ]);
        }

        if ($this->getRequest()->isPost()) {
            $this->form2->setData($this->getRequest()->getPost());

            if ($this->form2->isValid()) {
                return $this->conclude($this->form2);
            } else {
                return $this->signupForm($this->form2);
            }
        } else {
            return $this->signupForm($this->form2);
        }
    }

    private function conclude($form)
    {
        $form->registerData();

        $data = $this->registrationService->retrieveData();
        $data = $this->registrationService->formatData($data);
        try {
            $this->registrationService->notifySharengoByMail($data);
            $this->registrationService->saveData($data);
            $this->registrationService->sendEmail($data['email'], $data['surname'], $data['hash']);
            $this->registrationService->removeSessionData();
        } catch (\Exception $e) {
            $this->registrationService->notifySharengoErrorByEmail($e->getMessage());
            return $this->redirect()->toRoute('signup-2', array('lang' => $this->languageService->getLanguage()));
        }

        return $this->redirect()->toRoute('signup-3', array('lang' => $this->languageService->getLanguage()));
    }

    private function signupForm($form)
    {
        return new ViewModel([
            'form' => $form
        ]);
    }

    public function signup3Action()
    {
        return new ViewModel();
    }

    public function signupinsertAction()
    {
        $hash = $this->params()->fromQuery('user');

        $message = $this->registrationService->registerUser($hash);
        $email = '';
        $enablePayment = false;

        $user = $this->registrationService->getUserFromHash($hash);
        
        if (null != $user) {
            $urlencodedEmail = urlencode($user->getEmail());
            $enablePayment = !$user->getFirstPaymentCompleted();
        }

        return new ViewModel(array('message' => $message,
                                   'enable_payment' => $enablePayment,
                                   'email' => $urlencodedEmail));
    }
    
}
