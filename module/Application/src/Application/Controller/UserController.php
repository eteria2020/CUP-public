<?php

namespace Application\Controller;

// External Modules
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;
use Zend\Session\Container;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\DateTime;
use SharengoCore\Service\FleetService;
// Internal Modules
use Application\Service\RegistrationService;
use Multilanguage\Service\LanguageService;
use Application\Service\ProfilingPlaformService;
use Application\Exception\ProfilingPlatformException;
use Application\Form\RegistrationForm;
use SharengoCore\Service\CustomersService;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\Fleet;
use Zend\Log\Logger;
use SharengoCore\Service\EmailService as EmailService;

class UserController extends AbstractActionController {

    //variabile sessione
    private $smsVerification;

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
     * @var array
     */
    private $smsConfig;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var FleetService
     */
    private $fleetService;

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
    Form $form1, Form $form2, RegistrationService $registrationService, CustomersService $customersService, LanguageService $languageService, ProfilingPlaformService $profilingPlatformService, Translator $translator, HydratorInterface $hydrator, array $smsConfig, EmailService $emailService, FleetService $fleetService
    ) {

        $this->form1 = $form1;
        $this->form2 = $form2;
        $this->registrationService = $registrationService;
        $this->customersService = $customersService;
        $this->languageService = $languageService;
        $this->profilingPlatformService = $profilingPlatformService;
        $this->translator = $translator;
        $this->hydrator = $hydrator;
        $this->smsConfig = $smsConfig;
        $this->emailService = $emailService;
        $this->fleetService = $fleetService;
    }

    public function loginAction() {
        return new ViewModel();
    }

    public function signupAction() {


        //if there are mobile param change layout
        $mobile = $this->params()->fromRoute('mobile');
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
                return $this->proceed($this->form1, $formData['promocode'], $mobile);
            } else {
                return $this->signupForm($this->form1, $mobile);
            }
        } else {
            return $this->signupForm($this->form1, $mobile);
        }
    }

    public function signupScoreAction() {
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

    private function signupScoreUnknown($email) {
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
            $container = new Container('session');
            $container->offsetSet('hasDiscount', true);
        } catch (ProfilingPlatformException $ex) {

        }

        return $this->redirect()->toRoute('signup');
    }

    private function signupScoreKnown($customer) {
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

    private function proceed($form, $promoCode, $mobile) {
        $form->registerData($promoCode);
        return $this->redirect()->toRoute('signup-2', ['mobile' => $mobile]);
    }

    public function signup2Action() {
        //if there are mobile param change layout
        $mobile = $this->params()->fromRoute('mobile');
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
                return $this->conclude($this->form2, $mobile);
            } else {
                return $this->signupForm($this->form2, $mobile);
            }
        } else {
            return $this->signupForm($this->form2, $mobile);
        }
    }

    public function signupVerifyCodeAction($smsCode) {
        $smsVerification = new Container('smsVerification');
        if ($smsVerification->offsetGet('code') == $smsCode) {
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(true);
            return $response;
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(false);
            return $response;
        }
    }

    public function signupSmsAction() {
        $smsVerification = new Container('smsVerification');
        //$session_formValidation = new Container('formValidation');
        if (!$smsVerification->offsetExists('timeStamp')) {
            $smsVerification->offsetSet('timeStamp', new \DateTime());
            $smsVerification->offsetSet('mobile', $this->params()->fromPost('mobile'));
            $smsVerification->offsetSet('dialCode', $this->params()->fromPost('dialCode'));
            $smsVerification->offsetSet('code', $this->codeGenerator());
            $response_msg = $this->manageSendSms($smsVerification->offsetGet('dialCode'), $smsVerification->offsetGet('mobile'), $smsVerification->offsetGet('code'));
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent($response_msg);
            return $response;
        } else {

            $now = new \DateTime();
            $diffSeconds = $now->getTimestamp() - $smsVerification->offsetGet('timeStamp')->getTimeStamp();
            if ($diffSeconds > 60) {
                $smsVerification->offsetSet('timeStamp', new \DateTime());

                //in caso sbagliasse numero aggiorno il numero di telefono
                $smsVerification->offsetSet('mobile', $this->params()->fromPost('mobile'));
                $smsVerification->offsetSet('dialCode', $this->params()->fromPost('dialCode'));
                $smsVerification->offsetSet('code', $this->codeGenerator());

                $response_msg = $this->manageSendSms($smsVerification->offsetGet('dialCode'), $smsVerification->offsetGet('mobile'), $smsVerification->offsetGet('code'));
                $response = $this->getResponse();
                $response->setStatusCode(200);
                $response->setContent($response_msg);
                return $response;
            } else {
                $response = $this->getResponse();
                $response->setStatusCode(200);
                $response->setContent("Wait message");
                return $response;
            }
        }
    }

    /**
     * manageSendSms -> send message with sms hostig provider
     *
     *
     * @param int $dialCode - dialcode to phone number
     * @param int $mobile - phone nuember
     * @param int $code - random generate code
     * @return type
     */
    private function manageSendSms($dialCode, $mobile, $code) {

        $attachman = [];

        $translator = new \Zend\I18n\Translator\Translator();
        //invio sms
        $username = $this->smsConfig['username'];
        $password = $this->smsConfig['password'];

        $url = $this->smsConfig['url'];

        $textMsg = $this->smsConfig['text'] . $code;


        $fields = array(
            'sandbox' => $this->smsConfig['sandbox'],
            'to' => $dialCode . $mobile,
            'from' => $this->smsConfig['from'],
            'text' => utf8_encode($textMsg)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));

        $out = curl_exec($ch);
        $sms_msg = json_decode($out);

        $writerError = new \Zend\Log\Writer\Stream($this->smsConfig['logError']);
        $loggerError = new \Zend\Log\Logger();
        $loggerError->addWriter($writerError);

        $writeSuccess = new \Zend\Log\Writer\Stream($this->smsConfig['logSuccess']);
        $loggerSuccess = new \Zend\Log\Logger();
        $loggerSuccess->addWriter($writeSuccess);

        $response_message = "OK";

        //write case logError
        if (empty($out)) {
            //errore URL GENERICO
            //write log
            $loggerError->info('Errore generico prestare attenzione');
            $response_message = $translator->translate("Errore invio sms");
        } else {
            //if((strpos($out, "errorCode") != false)){
            if (isset($sms_msg->errorCode)) {//cambiaisset
                $errorCode = $sms_msg->errorCode;
                if ($errorCode == 400) {
                    switch ($sms_msg->errorMsg) {
                        case "NO_VALID_RECIPIENT":
                            //destinatario non corretto
                            //write log
                            $loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                            $response_message = $translator->translate("Numero di telefono non corretto");

                            break;

                        case "BAD_CREDIT":
                            //credito insufficente
                            //write log
                            $loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                            $response_message = $translator->translate("Errore invio sms");

                            $this->emailService->sendEmail(
                                    "ufficiotecnico@sharengo.eu", "Credito Esaurito SMS Hosting", "Il credito del servizio SMS Hostin è finito, per inviare nuovi sms ricaricare", $attachman
                            );

                            break;

                        case "BAD_TEXT":
                            //test errato
                            //write log
                            $loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                            $response_message = $translator->translate("Errore invio sms");

                            break;

                        case "GENERIC_ERROR":
                            //errore generico
                            //write log
                            $loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                            $response_message = $translator->translate("Errore invio sms");

                            $this->emailService->sendEmail(
                                    "ufficiotecnico@sharengo.eu", "Errore generico SMS Hosting", "Si è verificato un del servizio SMS Hostin, verificare i log /tmp/logErrorSms.txt e ", $attachman
                            );

                            break;

                        default:
                            //errore generico
                            //write log
                            $loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                            $response_message = $translator->translate("Errore invio sms");

                            break;
                    }
                } else if ($errorCode == 500) {
                    //errore generico
                    //write log
                    $loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);

                    $this->emailService->sendEmail(
                            "ufficiotecnico@sharengo.eu", "Errore generico SMS Hosting", "Si è verificato un del servizio SMS Hostin, verificare i log /tmp/logErrorSms.txt e ", $attachman
                    );
                } else if ($errorCode == 401) {
                    //credenziali sbagliate
                    //write log
                    $loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);

                    $this->emailService->sendEmail(
                            "ufficiotecnico@sharengo.eu", "Credenziali SMS Hosting MODIFICATE", "Sono  state modificate le credenziali del servizio di SMS Hosting, login fallito", $attachman
                    );
                } else if ($errorCode == 405) {
                    //metodo http non consentito
                    //write log
                    $loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                }
            } else {
                //write succes log
                $loggerSuccess->info(';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
            }
        }

        curl_close($ch);
        return $response_message;
    }

    /**
     * codeGenerator -> generate random code to sms validation in registration form
     * if sendbox is true (no send message) the code is 1234 (dafault)
     * else sendobox is false (SEND message) the code is random generate
     *
     * @return type
     */
    private function codeGenerator() {

        $sandbox = $this->smsConfig['sandbox'];
        if ($sandbox === "true") {
            $codice = 1234;
        } else {
            $codice = mt_rand(1000, 9999);
        }

        return $codice . "";
    }

    private function conclude($form, $mobile) {
        $form->registerData();

        $data = $this->registrationService->retrieveValidData();

        // if $data is empty it means that the session expired, so we redirect the user to the beginning of the registration
        if (empty($data)) {
            $message = $this->translator->translate('La sessione è scaduta. E\' necessario ripetere la procedura di registrazione');
            $this->flashMessenger()->addErrorMessage($message);
            return $this->redirect()->toRoute('signup', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
        }
        $data = $this->registrationService->formatData($data);
        
        try {
            $data = $this->registrationService->sanitizeDialMobile($data);
            $this->registrationService->notifySharengoByMail($data);
            $this->registrationService->saveData($data);
            $this->registrationService->sendEmail($data['email'], $data['name'], $data['surname'], $data['hash'], $data['language']);
            $this->registrationService->removeSessionData();
        } catch (\Exception $e) {
            $this->registrationService->notifySharengoErrorByEmail($e->getMessage() . ' ' . json_encode($e->getTrace()));
            return $this->redirect()->toRoute('signup-2', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
        }

        $this->getEventManager()->trigger('registrationCompleted', $this, $data);

        return $this->redirect()->toRoute('signup-3', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
    }

    private function signupForm($form, $mobile) {
        if ($mobile) {
            $this->layout('layout/map');
        }
        return new ViewModel([
            'form' => $form,
            'hasDiscount' => $this->customerHasDiscount(),
            'mobile' => $mobile
        ]);
    }

    public function signup3Action() {
        //if there are mobile param change layout
        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }
        return new ViewModel();
    }

    public function signupinsertAction() {
        $hash = $this->params()->fromQuery('user');

        $message = $this->registrationService->registerUser($hash);
        $enablePayment = false;

        $customer = $this->customersService->getUserFromHash($hash);

        if (null != $customer) {
            $enablePayment = !$customer->getFirstPaymentCompleted();
        }

        $needsDriversLicenseUpload = $this->customersService->customerNeedsToAcceptDriversLicenseForm($customer) &&
                !$this->customersService->customerHasAcceptedDriversLicenseForm($customer);

        //NOTE add 'customerEmail' and 'customerFleetId' only for Criteo use
        return new ViewModel([
            'message' => $message,
            'enable_payment' => $enablePayment,
            'customerId' => $customer->getId(),
            'customerEmail' => $customer->getEmail(),
            'customerFleetId' => $customer->getFleet()->getId(),
            'benefitsFromDiscountedSubscriptionAmount' => $customer->benefitsFromDiscoutedSubscriptionAmount(),
            'subscriptionDiscountedAmount' => $customer->findDiscountedSubscriptionAmount() / 100,
            'needsDriversLicenseUpload' => $needsDriversLicenseUpload,
            'hash' => $hash
        ]);
    }

    public function signupScoreCompletionAction() {
        return new ViewModel();
    }

    /**
     *  This action autocomplete the signup form "PromoCode" field,
     *  from the given root parameter "promocode".
     */
    public function promocodeSignupAction() {
        $promoCode = strtoupper($this->params('promocode'));

        $this->form1->registerPromoCodeData(['promocode' => $promoCode]);

        $this->redirect()->toRoute('signup');
    }

    private function customerHasDiscount() {
        $container = new Container('userDiscount');
        return $container->offsetGet('hasDiscount');
    }

    private function getProfilingPlatformPromoCode($email) {
        try {
            return $this->profilingPlatformService->getPromoCodeByEmail($email);
        } catch (ProfilingPlatformException $ex) {

        }

        return null;
    }

    private function getProfilingPlatformFleet($email) {
        try {
            return $this->profilingPlatformService->getFleetByEmail($email);
        } catch (ProfilingPlatformException $ex) {

        }

        return null;
    }

}
