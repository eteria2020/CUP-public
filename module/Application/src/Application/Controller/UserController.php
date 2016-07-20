<?php

namespace Application\Controller;

// External Modules
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;
use Zend\Session\Container;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;

// Internal Modules
use Application\Service\RegistrationService;
use Multilanguage\Service\LanguageService;
use Application\Service\ProfilingPlaformService;
use Application\Exception\ProfilingPlatformException;
use Application\Form\RegistrationForm;
use SharengoCore\Service\CustomersService;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\Fleet;

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
     * @var ProfilingPlaformService
     */
    private $profilingPlatformService;

    /**
     * @var \Zend\Mvc\I18n\Translator
     */
    private $translator;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param Form $form1
     * @param Form $form2
     * @param RegistrationService $registrationService
     * @param CustomersService $customersService
     * @param LanguageService $languageService
     * @param ProfilingPlaformService $profilingPlatformService
     * @param Translator $translator
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        Form $form1,
        Form $form2,
        RegistrationService $registrationService,
        CustomersService $customersService,
        LanguageService $languageService,
        ProfilingPlaformService $profilingPlatformService,
        Translator $translator,
        HydratorInterface $hydrator
    ) {
        $this->form1 = $form1;
        $this->form2 = $form2;
        $this->registrationService = $registrationService;
        $this->customersService = $customersService;
        $this->languageService = $languageService;
        $this->profilingPlatformService = $profilingPlatformService;
        $this->translator = $translator;
        $this->hydrator = $hydrator;
    }

    public function loginAction()
    {
        return new ViewModel();
    }

    public function signupAction()
    {
        //if there are data in session, we use them to populate the form
        $registeredData = $this->form1->getRegisteredData();
        $registeredDataPromoCode = $this->form1->getRegisteredDataPromoCode();

        if (!empty($registeredDataPromoCode)) {
            $this->form1->setData([
                'promocode' => $registeredDataPromoCode
            ]);
        }

        if (!empty($registeredData)) {
            $this->form1->setData([
                'user' => $registeredData->toArray($this->hydrator),
                'promocode' => $registeredDataPromoCode,
            ]);
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $this->form1->setData($formData);

            if ($this->form1->isValid()) {
                return $this->proceed($this->form1, $formData['promocode']);
            } else {
                return $this->signupForm($this->form1);
            }
        } else {
            return $this->signupForm($this->form1);
        }
    }

    public function signupScoreAction()
    {
        $email = strtolower(urldecode($this->params('email')));

        $customers = $this->customersService->findByEmail($email);
        $customer = null;
        if (count($customers) > 0) {
            $customer = $customers[0];
        }

        // Proceed only if it's a new customer for Sharengo platform
        if (null == $customer) {
            $this->signupScoreUnknown($email);
        } else {
            $this->signupScoreKnown($customer);
        }
    }

    private function signupScoreUnknown($email)
    {
        // Customer exists inside profiling platform?
        try {
            //throws an exception if the user doesn't have a discount
            $this->profilingPlatformService->getDiscountByEmail($email);

            // fill form data with available infos
            $customer = new Customers();
            $customer->setEmail($email);
            $customer->setProfilingCounter(1);

            $fleet = $this->getProfilingPlatformFleet($email);
            if ($fleet instanceof Fleet) {
                $customer->setFleet($fleet);
            }

            $this->form1->registerCustomerData($customer);

            $promoCode = $this->getProfilingPlatformPromoCode($email);
            $this->form1->registerPromoCodeData(['promocode' => $promoCode]);

            // we store in session the information that the user already have a discount, so we can avoid showing him the banner
            $container = new Container('userDiscount');
            $container->offsetSet('hasDiscount', true);
        } catch (ProfilingPlatformException $ex) {
        }

        return $this->redirect()->toRoute('signup');
    }

    private function signupScoreKnown($customer)
    {
        $this->customersService->increaseCustomerProfilingCounter($customer);

        try {
            if ($customer->getReprofilingOption() != 1 && $customer->getProfilingCounter() <= 2) {
                //throws an exception if the user doesn't have a discount
                $discount = $this->profilingPlatformService->getDiscountByEmail($customer->getEmail());

                $this->customersService->setCustomerDiscountRate($customer, $discount);
            }
        } catch (ProfilingPlatformException $ex) {
        }

        if ($customer->getFirstPaymentCompleted()) {
            return $this->redirect()->toRoute('signup-score-completion');
        } else {
            return $this->redirect()->toRoute('cartasi/primo-pagamento', [], ['query' => ['customer' => $customer->getId()]]);
        }
    }

    private function proceed($form, $promoCode)
    {
        $form->registerData($promoCode);

        return $this->redirect()->toRoute('signup-2');
    }

    public function signup2Action()
    {
        //if there are data in session, we use them to populate the form
        $registeredData = $this->form2->getRegisteredData();

        if (!empty($registeredData)) {
            $this->form2->setData([
                'driver' => $registeredData->toArray($this->hydrator)
            ]);
        }

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if (!isset($postData['driver']['driverLicenseCategories'])) {
                $driver = $postData['driver'];
                $driver['driverLicenseCategories'] = [];
                $postData->set('driver', $driver);
            }
            $this->form2->setData($postData);

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

        $data = $this->registrationService->retrieveValidData();

        // if $data is empty it means that the session expired, so we redirect the user to the beginning of the registration
        if (empty($data)) {
            $message = $this->translator->translate('La sessione è scaduta. E\' necessario ripetere la procedura di registrazione');
            $this->flashMessenger()->addErrorMessage($message);
            return $this->redirect()->toRoute('signup', ['lang' => $this->languageService->getLanguage()]);
        }
        $data = $this->registrationService->formatData($data);
        try {
            $this->registrationService->notifySharengoByMail($data);
            $this->registrationService->saveData($data);
            $this->registrationService->sendEmail($data['email'], $data['name'], $data['surname'], $data['hash']);
            $this->registrationService->removeSessionData();
        } catch (\Exception $e) {
            $this->registrationService->notifySharengoErrorByEmail($e->getMessage().' '.json_encode($e->getTrace()));
            return $this->redirect()->toRoute('signup-2', ['lang' => $this->languageService->getLanguage()]);
        }

        $this->getEventManager()->trigger('registrationCompleted', $this, $data);

        return $this->redirect()->toRoute('signup-3', ['lang' => $this->languageService->getLanguage()]);
    }

    private function signupForm($form)
    {
        return new ViewModel([
            'form' => $form,
            'hasDiscount' => $this->customerHasDiscount()
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
        $enablePayment = false;

        $customer = $this->customersService->getUserFromHash($hash);

        if (null != $customer) {
            $enablePayment = !$customer->getFirstPaymentCompleted();
        }

        $needsDriversLicenseUpload = $this->customersService->customerNeedsToAcceptDriversLicenseForm($customer) &&
            !$this->customersService->customerHasAcceptedDriversLicenseForm($customer);

        return new ViewModel([
            'message' => $message,
            'enable_payment' => $enablePayment,
            'customerId' => $customer->getId(),
            'benefitsFromDiscountedSubscriptionAmount' => $customer->benefitsFromDiscoutedSubscriptionAmount(),
            'subscriptionDiscountedAmount' => $customer->findDiscountedSubscriptionAmount() / 100,
            'needsDriversLicenseUpload' => $needsDriversLicenseUpload,
            'hash' => $hash
        ]);
    }

    public function signupScoreCompletionAction()
    {
        return new ViewModel();
    }

    /**
     *  This action autocomplete the signup form "PromoCode" field,
     *  from the given root parameter "promocode".
     */
    public function promocodeSignupAction()
    {
        $promoCode = strtoupper($this->params('promocode'));

        $this->form1->registerPromoCodeData(['promocode' => $promoCode]);

        $this->redirect()->toRoute('signup');
    }

    private function customerHasDiscount()
    {
        $container = new Container('userDiscount');
        return $container->offsetGet('hasDiscount');
    }

    private function getProfilingPlatformPromoCode($email)
    {
        try {
            return $this->profilingPlatformService->getPromoCodeByEmail($email);
        } catch (ProfilingPlatformException $ex) {
        }

        return null;
    }

    private function getProfilingPlatformFleet($email)
    {
        try {
            return $this->profilingPlatformService->getFleetByEmail($email);
        } catch (ProfilingPlatformException $ex) {
        }

        return null;
    }
}
