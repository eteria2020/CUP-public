<?php

namespace Application\Controller;

// External Modules

use Application\Form\SignupSK2Form;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\Session\Container;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

//use Zend\Stdlib\DateTime;

use MvLabsMultilanguage\Service\LanguageService;

use SharengoCode\Entity\Cards;
use SharengoCore\Entity\Configurations;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\Fleet;
use SharengoCore\Form\DTO\UploadedFile;
use SharengoCore\Service\ConfigurationsService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CustomerNoteService;
use SharengoCore\Service\EmailService as EmailService;
use SharengoCore\Service\ForeignDriversLicenseService;
use SharengoCore\Service\FleetService;
use SharengoCore\Service\PromoCodesACIService;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\PromoCodesMemberGetMemberService;
use SharengoCore\Service\PromoCodesOnceService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\UsersService;

//use Zend\Log\Logger;
use SMSGatewayMe\Client\ApiClient;
use SMSGatewayMe\Client\Configuration;
use SMSGatewayMe\Client\Api\MessageApi;
use SMSGatewayMe\Client\Model\SendMessageRequest;

// Internal Modules
use Application\Exception\ProfilingPlatformException;
use Application\Form\NewRegistrationForm;
use Application\Form\NewRegistrationForm2;
use Application\Form\OptionalRegistrationForm;
use Application\Form\RegistrationForm;
use Application\Form\RegistrationForm2;

use Application\Service\RegistrationService;
use Application\Service\ProfilingPlaformService;

class UserController extends AbstractActionController {

    //variabile sessione
    private $smsVerification;

    /**
     * @var \Application\Form\RegistrationForm
     */
    private $form1;

    /**
     * @var \Application\Form\RegistrationForm2
     */
    private $form2;

    /**
     * @var \Application\Form\NewRegistrationForm
     */
    private $newForm;

    /**
     * @var \Application\Form\NewRegistrationForm2
     */
    private $newForm2;

    /**
     * @var \Application\Form\SignupSK2Form
     */
    private $formSK2;

    /**
     * @var \Application\Form\OptionalRegistrationForm
     */
    private $optionalForm;

    /**
     * @var \Application\Service\RegistrationService
     */
    private $registrationService;

    /**
     * @var SharengoCore\Service\PromoCodesMemberGetMemberService
     */
    private $promoCodesMemberGetMemberService;

    /**
     *
     * @var \SharengoCore\Service\CustomersService
     */
    private $customersService;

     /**
     *
     * @var \SharengoCore\Service\CustomerNoteService
     */
    private $customerNoteService;

     /**
     *
     * @var \SharengoCore\Service\UsersService
     */
    private $usersService;

    /**
     *
     * @var \SharengoCore\Service\PromoCodeService
     */
    private $promoCodeService;

    /**
     *
     * @var \SharengoCore\Service\PromoCodesOnceService
     */
    private $promoCodesOnceService;

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
    private $config;

    /**
     * @var ConfigurationsService
     */
    private $configurationsService;
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
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var ForeignDriversLicenseService
     */
    private $foreignDriversLicenseService;
    
    /**
     * @var array
     */
    private $googleMapsConfig;

    /**
     * @var PromoCodesACIService
     */
    private $promoCodeACIService;

    /**
     * @var array
     */
    private $smsDbConfigurations;

    /**
     * @var array
     */
    private $smsGatewayMe;

    /**
     * @var $serverInstance
     */
    private $serverInstance = "";

    /**
     * UserController constructor.
     * @param RegistrationForm $form1
     * @param RegistrationForm2 $form2
     * @param NewRegistrationForm $newForm
     * @param NewRegistrationForm2 $newForm2
     * @param SignupSK2Form $formSK2
     * @param OptionalRegistrationForm $optionalForm
     * @param RegistrationService $registrationService
     * @param CustomersService $customersService
     * @param CustomerNoteService $customerNoteService
     * @param UsersService $usersService
     * @param LanguageService $languageService
     * @param ProfilingPlaformService $profilingPlatformService
     * @param Translator $translator
     * @param HydratorInterface $hydrator
     * @param array $config
     * @param ConfigurationsService $configurationsService
     * @param EmailService $emailService
     * @param FleetService $fleetService
     * @param TripsService $tripsService
     * @param PromoCodesService $promoCodeService
     * @param PromoCodesOnceService $promoCodesOnceService
     * @param PromoCodesMemberGetMemberService $promoCodesMemberGetMemberService
     * @param ForeignDriversLicenseService $foreignDriversLicenseService
     * @param PromoCodesACIService $promoCodeACIService
     */
    public function __construct(
        RegistrationForm $form1,
        RegistrationForm2 $form2,
        NewRegistrationForm $newForm,
        NewRegistrationForm2 $newForm2,
        SignupSK2Form $formSK2,
        OptionalRegistrationForm $optionalForm,
        RegistrationService $registrationService,
        CustomersService $customersService,
        CustomerNoteService $customerNoteService,
        UsersService $usersService,
        LanguageService $languageService,
        ProfilingPlaformService $profilingPlatformService,
        Translator $translator,
        HydratorInterface $hydrator,
        array $config,
        ConfigurationsService $configurationsService,
        EmailService $emailService,
        FleetService $fleetService,
        TripsService $tripsService,
        PromoCodesService $promoCodeService,
        PromoCodesOnceService $promoCodesOnceService,
        PromoCodesMemberGetMemberService $promoCodesMemberGetMemberService,
        ForeignDriversLicenseService $foreignDriversLicenseService,
        PromoCodesACIService $promoCodeACIService
    ) {
        $this->form1 = $form1;
        $this->form2 = $form2;
        $this->newForm = $newForm;
        $this->newForm2 = $newForm2;
        $this->formSK2 = $formSK2;
        $this->optionalForm = $optionalForm;
        $this->registrationService = $registrationService;
        $this->customersService = $customersService;
        $this->customerNoteService = $customerNoteService;
        $this->usersService = $usersService;
        $this->languageService = $languageService;
        $this->profilingPlatformService = $profilingPlatformService;
        $this->translator = $translator;
        $this->hydrator = $hydrator;
        $this->config = $config;
        $this->configurationsService = $configurationsService;
        $this->emailService = $emailService;
        $this->fleetService = $fleetService;
        $this->tripsService = $tripsService;
        $this->promoCodeService = $promoCodeService;
        $this->promoCodesOnceService = $promoCodesOnceService;
        $this->promoCodesMemberGetMemberService = $promoCodesMemberGetMemberService;
        $this->foreignDriversLicenseService = $foreignDriversLicenseService;
        $this->promoCodeACIService = $promoCodeACIService;

        $this->smsConfig = $this->config['sms'];
        $this->smsDbConfigurations = $this->configurationsService->getConfigurationsKeyValueBySlug(Configurations::SMS);
        $this->googleMapsConfig = $this->config['googleMaps'];
        $this->smsGatewayMe = $this->config['smsGatewayMe'];

        if(isset($this->config['serverInstance'])) {
            $this->serverInstance = $this->config['serverInstance'];
        }
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

    public function co2Action() {

        //get $customerId param in post && get customer from $customerId && get all trip from $customerId
        $customerId = $this->params()->fromPost('id');
        $customer = $this->customersService->findById($customerId);
        $trips = $this->tripsService->getTripsByCustomerCO2($customerId);

        $kgOfCo2Save = "";
        define("GR_CO2_KM", 106); //constant
        //$GR_CO2_KM = 106; //constant
        $secondsTrips = 0;

        //$Vm is different for a city, get from customer fleetId
        $averageSpeed = 20; // defaul, id the fleet is not present
        switch ($customer->getFleet()->getId()):
            case 1:
                $averageSpeed = 17;
                break;
            case 2:
                $averageSpeed = 15;
                break;
            case 3:
                $averageSpeed = 15;
                break;
            case 4:
                $averageSpeed = 20;
                break;
        endswitch;

        foreach ($trips as $trip) {
            //diff between timeStamp_end trip (timeStamp_end - parkSecondo) and timeStamp_start trip
            $parkseconds = 0;
            if (!is_null($trip->getParkSeconds())) {
                $parkseconds = $trip->getParkSeconds();
            }
            $timeTrip = date_diff($trip->getEndTx()->modify("-" . $parkseconds . " second"), $trip->getTimestampBeginning());
            $secondsTrips += $this->calculateTripInSecond($timeTrip);
        }

        //KG = ((((secondi corsa/60)/60) * VM)* GR/KM)/1000
        $kgOfCo2Save = (((($secondsTrips / 60) / 60) * $averageSpeed) * GR_CO2_KM) / 1000;
        $kgOfCo2Save = round($kgOfCo2Save, 0);

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent($kgOfCo2Save);
        return $response;
    }

    /**
     * Get seconds of $timeTrip DateInterval
     * @param type $timeTrip
     * @return type
     */
    private function calculateTripInSecond($timeTrip) {

        $seconds = 0;

        $days = $timeTrip->format('%a');
        if ($days) {
            $seconds += 24 * 60 * 60 * $days;
        }
        $hours = $timeTrip->format('%H');
        if ($hours) {
            $seconds += 60 * 60 * $hours;
        }
        $minutes = $timeTrip->format('%i');
        if ($minutes) {
            $seconds += 60 * $minutes;
        }
        $seconds += $timeTrip->format('%s');

        return $seconds;
    }

    /**
     *
     * Send sms check code
     * @return type check code status
     */
    public function signupSmsAction() {
        $smsVerification = new Container('smsVerification');

        //CSD-1142 - check if mobile number already exixts
        if ($this->checkDuplicateMobileAction() > 0) {
            if ($this->checkTheSameModifyNumber()) {
                $response = $this->getResponse();
                $response->setStatusCode(200);
                $response->setContent("Found");
                return $response;
            }
        }

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
            $this->signupSmsCustomerNote($this->params()->fromPost('email'), $this->params()->fromPost('mobile'), $this->smsConfig['text'] . $smsVerification->offsetGet('code'));
            return $response;
        } else {

            $now = new \DateTime();
            $diffSeconds = $now->getTimestamp() - $smsVerification->offsetGet('timeStamp')->getTimeStamp();
            if ($diffSeconds > 50) {
                $smsVerification->offsetSet('timeStamp', new \DateTime());

                //in caso sbagliasse numero aggiorno il numero di telefono
                $smsVerification->offsetSet('mobile', $this->params()->fromPost('mobile'));
                $smsVerification->offsetSet('dialCode', $this->params()->fromPost('dialCode'));
                $smsVerification->offsetSet('code', $this->codeGenerator());

                $response_msg = $this->manageSendSms($smsVerification->offsetGet('dialCode'), $smsVerification->offsetGet('mobile'), $smsVerification->offsetGet('code'));
                $response = $this->getResponse();
                $response->setStatusCode(200);
                $response->setContent($response_msg);
                $this->signupSmsCustomerNote($this->params()->fromPost('email'), $this->params()->fromPost('mobile'), $this->smsConfig['text'] . $smsVerification->offsetGet('code'));
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
     * @return string
     */
    private function manageSendSms($dialCode, $mobile, $code) {
        if($this->smsDbConfigurations["smsgatewayme"] == "true") { //db table configurations
            $smsGateway = $this->manageSmsGateway($dialCode, $mobile, $code);

            if (!is_null($smsGateway)) {
                return $smsGateway;
            }
        }

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

        //$writerError = new \Zend\Log\Writer\Stream($this->smsConfig['logError']);
        //$loggerError = new \Zend\Log\Logger();
        //$loggerError->addWriter($writerError);

        $writeSuccess = new \Zend\Log\Writer\Stream($this->smsConfig['logSuccess']);
        $loggerSuccess = new \Zend\Log\Logger();
        $loggerSuccess->addWriter($writeSuccess);

        $response_message = "OK";

        //write case logError
        if (empty($out)) {
            //errore URL GENERICO
            //write log
            //$loggerError->info('Errore generico prestare attenzione');
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
                            //$loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                            $response_message = $translator->translate("Numero di telefono non corretto");

                            break;

                        case "BAD_CREDIT":
                            //credito insufficente
                            //write log
                            //$loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                            $response_message = $translator->translate("Errore invio sms");

                            $this->emailService->sendEmail(
                                    "ufficiotecnico@sharengo.eu", "Credito Esaurito SMS Hosting", "Il credito del servizio SMS Hostin è finito, per inviare nuovi sms ricaricare", $attachman
                            );

                            break;

                        case "BAD_TEXT":
                            //test errato
                            //write log
                            //$loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                            $response_message = $translator->translate("Errore invio sms");

                            break;

                        case "GENERIC_ERROR":
                            //errore generico
                            //write log
                            //$loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                            $response_message = $translator->translate("Errore invio sms");

                            $this->emailService->sendEmail(
                                    "ufficiotecnico@sharengo.eu", "Errore generico SMS Hosting", "Si è verificato un del servizio SMS Hostin, verificare i log /tmp/logErrorSms.txt e ", $attachman
                            );

                            break;

                        default:
                            //errore generico
                            //write log
                            //$loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                            $response_message = $translator->translate("Errore invio sms");

                            break;
                    }
                } else if ($errorCode == 500) {
                    //errore generico
                    //write log
                    //$loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);

                    $this->emailService->sendEmail(
                            "ufficiotecnico@sharengo.eu", "Errore generico SMS Hosting", "Si è verificato un del servizio SMS Hostin, verificare i log /tmp/logErrorSms.txt e ", $attachman
                    );
                } else if ($errorCode == 401) {
                    //credenziali sbagliate
                    //write log
                    //$loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);

                    $this->emailService->sendEmail(
                            "ufficiotecnico@sharengo.eu", "Credenziali SMS Hosting MODIFICATE", "Sono  state modificate le credenziali del servizio di SMS Hosting, login fallito", $attachman
                    );
                } else if ($errorCode == 405) {
                    //metodo http non consentito
                    //write log
                    //$loggerError->info('Error: ' . $sms_msg->errorCode . ';' . $sms_msg->errorMsg . ';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
                }
            } else {
                //write succes log
                //$loggerSuccess->info(';DialCode: ' . $dialCode . ';Mobile: ' . $mobile . ';Sms text: ' . $fields['text']);
            }
        }

        curl_close($ch);
        return $response_message;
    }

    /**
     *
     * @param $dialCode
     * @param $mobile
     * @param $code
     *
     * @return string|null
     */
    private function manageSmsGateway($dialCode, $mobile, $code){
        $id = $this->sendSmsGateway($dialCode, $mobile, $code);
        if(!is_null($id)){
            sleep($this->smsGatewayMe["wait"]);
            $messageStatus = $this->getSMSGatewayStatus($id);
            if (!is_null($messageStatus) && ($messageStatus == 'queued' || $messageStatus == 'sent')){
                return "OK";
            }
        }
        return null;
    }

    private function sendSmsGateway($dialCode, $mobile, $code){
        $config = Configuration::getDefaultConfiguration();
        $config->setApiKey('Authorization', $this->smsGatewayMe["token"]);
        $apiClient = new ApiClient($config);
        $messageClient = new MessageApi($apiClient);
        $dialCode = "+" . $dialCode;

        // Sending a SMS Message
        $sendMessageRequest = new SendMessageRequest([
            'phoneNumber' => $dialCode . $mobile,
            'message' => "Sharengo - " . $this->smsConfig['text'] . $code,
            'deviceId' => $this->smsGatewayMe["deviceId"]
        ]);

        try {
            $sendMessages = $messageClient->sendMessages([
                $sendMessageRequest,
            ]);
        } catch (\SMSGatewayMe\Client\ApiException $e){
            return null;
        }

        if(isset($sendMessages[0]) && $sendMessages[0] instanceof \SMSGatewayMe\Client\Model\Message){
            return $sendMessages[0]->getId();
        } else {
            return null;
        }
    }

    /**
     * @param $messageId
     * @return string|null
     */

    private function getSMSGatewayStatus($messageId){

        $config = Configuration::getDefaultConfiguration();
        $config->setApiKey('Authorization', $this->smsGatewayMe["token"]);
        $apiClient = new ApiClient($config);
        $messageClient = new MessageApi($apiClient);

        //Get SMS Message Information
        try {
            $message = $messageClient->getMessage($messageId);
        } catch(\SMSGatewayMe\Client\ApiException $e){
            return null;
        }

        if ($message instanceof \SMSGatewayMe\Client\Model\Message){
            return $message->getStatus();
        } else {
            return null;
        }
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

    /**
     * Insert a row in CustomersNote, for Admin check
     * 
     * @param string $email
     * @param string $mobile
     * @param string $message
     */
    private function signupSmsCustomerNote($email, $mobile, $message) {

        try {
            $customers = $this->customersService->findByEmail($email);
            if(count($customers)>0) {
                $customer = $customers[0];
                $webuser = $this->usersService->findUserById(12);

                if(!is_null($customer) && !is_null($webuser)) {
                    $this->customerNoteService->addNote($customer, $webuser, "SmsHosting;" . $mobile . ";" . $message);
                }
            }
        } catch (Exception $ex) {

        }

    }

    private function conclude($form, $mobile) {
        $form->registerData();

        $data = $this->registrationService->retrieveValidData();

        // if $data is empty it means that the session expired, so we redirect the user to the beginning of the registration
        if (empty($data)) {
            $message = $this->translator->translate('La sessione è scaduta. E\' necessario ripetere la procedura di registrazione');
            $this->flashMessenger()->addErrorMessage($message);
            //return $this->redirect()->toRoute('signup', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('signup', ['mobile' => $mobile]);
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
            //return $this->redirect()->toRoute('signup-2', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('signup-2', ['mobile' => $mobile]);
        }

        if (!is_null($data['email'])){
            $customer = $this->customersService->findByEmail($data['email'])[0];
            $this->customersService->assignCard($customer);
        }

        $this->getEventManager()->trigger('registrationCompleted', $this, $data);

        //return $this->redirect()->toRoute('signup-3', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
        return $this->redirect()->toRoute('signup-3', ['mobile' => $mobile]);
    }

    private function signupForm($form, $mobile) {
        if ($mobile) {
            $this->layout('layout/map');
        }
        return new ViewModel([
            'form' => $form,
            'hasDiscount' => $this->customerHasDiscount(),
            'mobile' => $mobile,
            'fleets' => $this->fleetService->getAllFleetsNoDummy()
        ]);
    }

    public function signup3Action() {
/*        $hash = $this->params()->fromQuery('user');
        $customer = $this->customersService->getUserFromHash($hash);*/

        //if there are mobile param change layout
        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }
        return new ViewModel([
/*            'customerFleetId' => $customer->getFleet()->getId(),
            'customerEmail' => $customer->getEmail()*/
        ]);
    }

    public function signupinsertAction() {
        $hash = $this->params()->fromQuery('user');

        $message = $this->registrationService->registerUser($hash);
        $enablePayment = false;

        $customer = $this->customersService->getUserFromHash($hash);

        if (!is_null($customer)) {
            $enablePayment = !$customer->getFirstPaymentCompleted();

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
        }else{
            return new ViewModel([
                'message' => $message,
                'enable_payment' => $enablePayment,
                'hash' => $hash
            ]);
        }
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
        $this->newForm2->registerPromoCodeData(['promocode' => $promoCode]);

        $this->redirect()->toRoute('signup');
    }

    public function promocodeVerifyAction() { // momo controle if the promo code is ok. return the min and eur.
        $promoCodeInfo = null;

        $pc = trim(strtoupper($this->getRequest()->getPost('promocode')));
        if ($this->promoCodeService->isValid(strtoupper($pc))) {
            $promoCode = $this->promoCodeService->getPromoCode($pc);
            $promoCodeInfo = $promoCode->getPromoCodesInfo();
        } else {
            if ($this->promoCodesOnceService->isValid($pc)) {
                $promoCodeOnce = $this->promoCodesOnceService->getByPromoCode($pc);
                $promoCodeInfo = $promoCodeOnce->getPromoCodesInfo();
            } else {
                if ($this->promoCodesMemberGetMemberService->isValid($pc)) {
                    $pcMgm = $this->promoCodesMemberGetMemberService->getPromoCodeNameWidthoutCustomerId($pc, true);
                    $promoCode = $this->promoCodeService->getPromoCode($pcMgm);
                    $promoCodeInfo = $promoCode->getPromoCodesInfo();
                } /*else {
                    if ($this->promoCodeACIService->isValid($pc)){
                        $promoCode = $this->promoCodeACIService->getByPromoCode();
                        $promoCodeInfo = $promoCode->getPromocodesinfo();
                    }
                }*/
            }
        }

        if (is_null($promoCodeInfo)) {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            $response->setContent(false);
        } else {
            $info['min'] = $promoCodeInfo->getMinutes();
            $info['cost'] = $promoCodeInfo->getOverriddenSubscriptionCost() / 100;
            $info['disc'] = $promoCodeInfo->discountPercentage();

            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode($info));
        }

        return $response;
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

    /**
     *
     * Check if mobile number already exixts
     *
     * Check without dial code to evaluate numbers already in the DB
     *
     * @return int      0 = not found
     *                  >0 = found
     */
    private function checkDuplicateMobileAction() {
        //$value = sprintf('%s%s',$this->params()->fromPost('dialCode'), $this->params()->fromPost('mobile'));
        $found = $this->customersService->checkMobileNumber($this->params()->fromPost('mobile'));
        return $found;
    }

    public function checkTheSameModifyNumber() {
        $customer = $this->customersService->findByEmail($this->params()->fromPost('email'));
        if (count($customer) > 0) {
            if ($customer[0]->getMobile() == $this->params()->fromPost('mobile')) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public function signupSK1Action() {
        //if there are mobile param change layout
        $mobile = $this->params()->fromRoute('mobile');

        $customerSession = $this->registrationService->getSignupCustomerSession();
        //error_log(var_dump($customerSession));
        if(!is_null($customerSession) && !$this->registrationService->isRegistrationCompleted($customerSession)){
            //return $this->redirect()->toRoute('signupSK2', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('signupSK2', ['mobile' => $mobile]);
        }
        //if there are data in session, we use them to populate the form
        $registeredData = $this->newForm->getRegisteredData();

        if (!empty($registeredData)) {
            $this->newForm->setData([
                'user' => $registeredData->toArray($this->hydrator)
            ]);
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $this->newForm->setData($formData);
            if ($this->newForm->isValid()) {
                return $this->signupSK1Conclude($this->newForm, $mobile);
            } else {
                return $this->newForm($this->newForm, $mobile);
            }
        } else {
            return $this->newForm($this->newForm, $mobile);
        }
    }

    public function signupSK2Action(){

        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }

        $customerSession = $this->registrationService->getSignupCustomerSession();

        /* redirect if session is empty */
        if(empty($customerSession)){
            return $this->redirect()->toRoute('signupSK1', ['mobile' => $mobile]);
        }

        if($this->registrationService->isRegistrationCompleted($customerSession)){
            return $this->redirect()->toRoute('area-utente', ['mobile' => $mobile]);
        }

        $registeredData = $this->formSK2->getRegisteredData();

        if (!empty($registeredData)) {
            $this->formSK2->setData([
                'user1' => $registeredData->toArray($this->hydrator)
            ]);
        }

        if ($this->getRequest()->isPost()) {

            $formData = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $this->formSK2->setData($formData);
            if ($this->formSK2->isValid()) {
                return $this->signupSK2Conclude($formData, $customerSession, $this->handleForeignUploadFiles($formData), $mobile);
            } else {
                $email = '';
                if ($customerSession instanceof Customers){
                    $email = $customerSession->getEmail();
                }
                return $this->formSK2($this->formSK2, $email, $mobile);
            }
        } else {
            $email = '';
            if ($customerSession instanceof Customers){
                $email = $customerSession->getEmail();
            }
            return $this->formSK2($this->formSK2, $email, $mobile);
        }
    }

    /* === NEW SIGNUP  === */

    public function newSignupAction() {
        //if there are mobile param change layout
        $mobile = $this->params()->fromRoute('mobile');

        if(isset($this->serverInstance["id"])) {
            if($this->serverInstance["id"]=="sk_SK") {
                return $this->redirect()->toRoute('signupSK1', ['mobile' => $mobile],['query' => ['lang' => 'sk_SK']]);
            }
        }

        $customerSession = $this->registrationService->getSignupCustomerSession();

        if(!is_null($customerSession) && !$this->registrationService->isRegistrationCompleted($customerSession)){
            //return $this->redirect()->toRoute('new-signup-2', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('new-signup-2', ['mobile' => $mobile]);
        }
        //if there are data in session, we use them to populate the form
        $registeredData = $this->newForm->getRegisteredData();

        if (!empty($registeredData)) {
            $this->newForm->setData([
                'user' => $registeredData->toArray($this->hydrator)
            ]);
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $this->newForm->setData($formData);
            if ($this->newForm->isValid()) {
                return $this->newConclude($this->newForm, $mobile);
            } else {
                return $this->newForm($this->newForm, $mobile);
            }
        } else {
            return $this->newForm($this->newForm, $mobile);
        }
    }


    private function newForm($newForm, $mobile) {
        if ($mobile) {
            $this->layout('layout/map');
        }

        return new ViewModel([
            'form' => $newForm,
            'mobile' => $mobile,
            'fleets' => $this->fleetService->getAllFleetsNoDummy()
        ]);
    }

    private function newConclude($form, $mobile) {
        $form->registerData();

        $data = $this->registrationService->newRetrieveValidData();

        // if $data is empty it means that the session expired, so we redirect the user to the beginning of the registration
        if (empty($data)) {
            $message = $this->translator->translate('La sessione è scaduta. E\' necessario ripetere la procedura di registrazione');
            $this->flashMessenger()->addErrorMessage($message);
            //return $this->redirect()->toRoute('new-signup', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('new-signup', ['mobile' => $mobile]);
        }
        $data = $this->registrationService->formatData1($data);

        try {
            $this->registrationService->notifySharengoByMail($data);
            $customer = $this->registrationService->saveData1($data);
            // $this->registrationService->sendEmail($data['email'], '', '', $data['hash'], $data['language']);
            $this->registrationService->removeSessionData1();

        } catch (\Exception $e) {
            $this->registrationService->notifySharengoErrorByEmail($e->getMessage() . ' ' . json_encode($e->getTrace()));
            //return $this->redirect()->toRoute('new-signup', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('new-signup', ['mobile' => $mobile]);
        }

        $this->getEventManager()->trigger('firstFormCompleted', $this, $data);
        $signupSession = new Container('newSignup');
        $signupSession->offsetSet("customer", $customer);
        //return $this->redirect()->toRoute('new-signup-2', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
        return $this->redirect()->toRoute('new-signup-2', ['mobile' => $mobile]);
    }

    private function signupSK1Conclude($form, $mobile) {
        $form->registerData();

        $data = $this->registrationService->newRetrieveValidData();

        // if $data is empty it means that the session expired, so we redirect the user to the beginning of the registration
        if (empty($data)) {
            $message = $this->translator->translate('La sessione è scaduta. E\' necessario ripetere la procedura di registrazione');
            $this->flashMessenger()->addErrorMessage($message);
            //return $this->redirect()->toRoute('new-signup', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('signupSK1', ['mobile' => $mobile]);
        }
        $data = $this->registrationService->formatData1($data);

        try {
            $this->registrationService->notifySharengoByMail($data);
            $customer = $this->registrationService->saveData1($data);
            // $this->registrationService->sendEmail($data['email'], '', '', $data['hash'], $data['language']);
            $this->registrationService->removeSessionData1();

        } catch (\Exception $e) {
            $this->registrationService->notifySharengoErrorByEmail($e->getMessage() . ' ' . json_encode($e->getTrace()));
            //return $this->redirect()->toRoute('new-signup', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('signupSK1', ['mobile' => $mobile]);
        }

        $this->getEventManager()->trigger('firstFormCompleted', $this, $data);
        $signupSession = new Container('newSignup');
        $signupSession->offsetSet("customer", $customer);

        return $this->redirect()->toRoute('signupSK2', ['mobile' => $mobile]);
    }




    public function newSignup2Action(){

        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }

        if(isset($this->serverInstance["id"])) {
            if($this->serverInstance["id"]=="sk_SK") {
                return $this->redirect()->toRoute('signupSK2', ['mobile' => $mobile],['query' => ['lang' => 'sk_SK']]);
            }
        }

        $customerSession = $this->registrationService->getSignupCustomerSession();

        /* redirect if session is empty */
        if(empty($customerSession)){
            //return $this->redirect()->toRoute('new-signup', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('new-signup', ['mobile' => $mobile]);
        }

        if($this->registrationService->isRegistrationCompleted($customerSession)){
            //return $this->redirect()->toRoute('area-utente', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('area-utente', ['mobile' => $mobile]);
        }

        $registeredData = $this->newForm2->getRegisteredData();
        $registeredDataPromoCode = $this->newForm2->getRegisteredDataPromoCode();

        if (!empty($registeredDataPromoCode)) {
            $this->newForm2->setData([
                'promocode' => $registeredDataPromoCode
            ]);
        }
        if (!empty($registeredData)) {
            $this->newForm2->setData([
                'user1' => $registeredData->toArray($this->hydrator),
                'promocode' => $registeredDataPromoCode
            ]);
        }

        if ($this->getRequest()->isPost()) {

            $formData = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $this->newForm2->setData($formData);
            if ($this->newForm2->isValid()) {
                return $this->newConclude2($this->newForm2, $formData['promocode'], $formData['user1']['civico'], $customerSession, $this->handleForeignUploadFiles($formData), $mobile);
            } else {
                $email = '';
                if ($customerSession instanceof Customers){
                    $email = $customerSession->getEmail();
                }
                return $this->newForm2($this->newForm2, $email, $mobile);
            }
        } else {
                $email = '';
                if ($customerSession instanceof Customers){
                    $email = $customerSession->getEmail();
                }
               return $this->newForm2($this->newForm2, $email, $mobile);
        }
    }

    private function newForm2($newForm2, $email, $mobile) {
        if ($mobile) {
            $this->layout('layout/map');
        }

        $customer = null;
        $customerEmail = $email;

        if($email != ''){
            $customer = $this->customersService->findOneByEmail($email);
            $email = explode('@', $email)[0] . '@';
        }

        $customerFleet = null;
        if(!is_null($customer) && !is_null($customer->getFleet())){
            $customerFleet = $customer->getFleet()->getId();
        }

        return new ViewModel([
            'form' => $newForm2,
            'email' => $email,
            'customerEmail' => $customerEmail,
            'mobile' => $mobile,
            'customerFleetId' => $customerFleet,
        ]);
    }

    private function formSK2($newForm2, $email, $mobile) {
        if ($mobile) {
            $this->layout('layout/map');
        }

        $customer = null;
        $customerEmail = $email;

        if($email != ''){
            $customer = $this->customersService->findOneByEmail($email);
            $email = explode('@', $email)[0] . '@';
        }

        $customerFleet = null;
        if(!is_null($customer) && !is_null($customer->getFleet())){
            $customerFleet = $customer->getFleet()->getId();
        }

        return new ViewModel([
            'form' => $newForm2,
            'email' => $email,
            'customerEmail' => $customerEmail,
            'mobile' => $mobile,
            'customerFleetId' => $customerFleet,
        ]);
    }

//    private function handleForeignUploadFiles($formData){
//        $uploadFile = [];
//        $signature = $formData['signature'];
//        if($formData['user1']['driverLicenseForeign'] == 'true') {
//            $driverLicenseFront = $formData['drivers-license-front'];
//            $driverLicenseFrontFile = new UploadedFile($formData['drivers-license-front']['name'], $formData['drivers-license-front']['type'], $formData['drivers-license-front']['tmp_name'], $formData['drivers-license-front']['size']);
//            $driverLicenseBack = $formData['drivers-license-back'];
//            $driverLicenseBackFile = new UploadedFile($formData['drivers-license-back']['name'], $formData['drivers-license-back']['type'], $formData['drivers-license-back']['tmp_name'], $formData['drivers-license-back']['size']);
//            $identityFront = $formData['identity-front'];
//            $identityFrontFile = new UploadedFile($formData['identity-front']['name'], $formData['identity-front']['type'], $formData['identity-front']['tmp_name'], $formData['identity-front']['size']);
//            $identityBack = $formData['identity-back'];
//            $identityBackFile = new UploadedFile($formData['identity-back']['name'], $formData['identity-back']['type'], $formData['identity-back']['tmp_name'], $formData['identity-back']['size']);
//            $uploadFile = [$driverLicenseFrontFile, $driverLicenseBackFile, $identityFrontFile, $identityBackFile];
//        } else {
//            $driverLicenseFront = null;
//            $driverLicenseBack = null;
//            $identityFront = null;
//            $identityBack = null;
//        }
//        return ["uploadedFile" => $uploadFile, "files" => ['signature' => $signature, 'drivers-license-front' => $driverLicenseFront, 'drivers-license-back' => $driverLicenseBack, 'identity-front' => $identityFront, 'identity-back' => $identityBack]];
//    }

    private function handleForeignUploadFiles($formData){
        $uploadFile = [];
        $result = [];
        $signature = $formData['signature'];
        $arrayOfFilesLabel = ['drivers-license-front', 'drivers-license-back', 'identity-front', 'identity-back', 'selfie'];

        $files = [];
        $files["signature"] = $signature;
        $i = 1;
        if($formData['user1']['driverLicenseForeign'] == 'true') {
            foreach($arrayOfFilesLabel as $label) {
                if (isset($formData[$label])) {
                    $upload = new UploadedFile($i.$formData[$label]['name'], $formData[$label]['type'], $formData[$label]['tmp_name'], $formData[$label]['size']);
                    array_push($uploadFile, $upload);
                    $files[$label] = $formData[$label];
                } else {
                    $files[$label] = null;
                }
                $i++;
            }
        } else {
            $files['drivers-license-front'] = null;
            $files['drivers-license-back'] = null;
            $files['identity-front'] = null;
            $files['identity-back'] = null;
            $files['selfie'] = null;
        }

        $result["uploadedFile"] = $uploadFile;
        $result["files"] = $files;

        return $result;
    }

    private function newConclude2($form, $promocode, $civico, $customer, $files, $mobile) {
        $form->registerData($promocode);

        $data = $this->registrationService->retrieveValidData2($civico, $files["files"]);

        // if $data is empty it means that the session expired, so we redirect the user to the beginning of the registration
        if (empty($data) || !($customer instanceof Customers)) {
            $message = $this->translator->translate('La sessione è scaduta. E\' necessario ripetere la procedura di registrazione');
            $this->flashMessenger()->addErrorMessage($message);
            //return $this->redirect()->toRoute('new-signup-2', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('new-signup-2', ['mobile' => $mobile]);
        }
        $data = $this->registrationService->formatData2($data, $civico, $customer);

        try {
            $data = $this->registrationService->sanitizeDialMobile($data);
            $customer = $this->registrationService->updateData2($data);
            $this->registrationService->removeSessionData2();

            if (!empty($files["uploadedFile"])){
                $this->foreignDriversLicenseService->saveUploadedFiles(
                    $files["uploadedFile"],
                    $customer
                );
            }

        } catch (\Exception $e) {
            $this->registrationService->notifySharengoErrorByEmail($e->getMessage() . ' ' . json_encode($e->getTrace()));
            //return $this->redirect()->toRoute('new-signup-2', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('new-signup-2', ['mobile' => $mobile]);
        }
        $this->getEventManager()->trigger('secondFormCompleted', $this, $data); //driver license validation
        $signupSession = new Container('newSignup');
        $signupSession->offsetSet("customer", $customer);

        return $this->redirect()->toRoute('cartasi/primo-pagamento',[], ['query' => ['customer' => $customer->getId(), 'signup' => true]] );
    }

    private function signupSK2Conclude($formData, Customers $customer, $files, $mobile) {

        $data = [];

        foreach ($formData['user1'] as $key => $value) {
            if (!is_null($value)) {
                $data[$key] = $formData['user1'][$key];
            }
        }

        // if $data is empty it means that the session expired, so we redirect the user to the beginning of the registration
        if (empty($data) || !($customer instanceof Customers)) {
            $message = $this->translator->translate('La sessione è scaduta. E\' necessario ripetere la procedura di registrazione');
            $this->flashMessenger()->addErrorMessage($message);
            return $this->redirect()->toRoute('signupSK1', ['mobile' => $mobile]);
        }

        try {
            $customer = $this->customersService->findById($customer->getId());

            //var_dump($data);
            $customer->setGender($data['gender']);
            $customer->setName($data['name']);
            $customer->setSurname($data['surname']);
            $customer->setTown($data['town']);
            $customer->setLanguage('sk');

//            $customer->setCountry($data['country']);
//            $customer->setProvince($data['province']);

            $customer->setAddress($data['address'].' '. $data['civico']);
            $customer->setZipCode($data['zipCode']);

            $customer->setBirthDate(date_create_from_format("d-m-Y H:i:s", $data['birthDate']." 00:00:00"));
            $customer->setBirthCountry($data['birthCountry']);
            $customer->setBirthProvince($data['birthProvince']);
            $customer->setBirthTown($data['birthTown']);

            $customer->setMobile('+'.$data['dialCode'].$data['mobile']);

            $customer->setTaxCode($data['taxCode']);

            $customer->setDriverLicenseName($data['name']);
            $customer->setDriverLicenseSurname($data['surname']);
            $customer->setDriverLicense($data['driverLicense']);
            $data['driverLicenseExpire'] = (!isset($data['driverLicenseExpire']) || (isset($data['driverLicenseExpire']) && ($data['driverLicenseExpire'] == "" || is_null($data['driverLicenseExpire'])))) ? date('d-m-Y', strtotime('+5 years')) : $data['driverLicenseExpire'];
            $customer->setDriverLicenseExpire(date_create_from_format("d-m-Y H:i:s",$data['driverLicenseExpire']." 00:00:00"));
            $customer->setDriverLicenseForeign(true);
            $customer->setDriverLicenseCountry("sk");
            $customer->setDriverLicenseAuthority('MC');

            $customer->setGeneralCondition1(true);
            $customer->setGeneralCondition2(true);
            $customer->setRegulationCondition1(true);
            $customer->setRegulationCondition2(true);
            $customer->setPrivacyCondition(true);
            $customer->setPrivacyInformation(true);
            $customer->setNewsletter(true);
            $customer->setRegistrationCompleted(true);

//            $data = $this->registrationService->sanitizeDialMobile($data);
            $customer = $this->registrationService->updateDataSK2($customer, $data);
//            $this->registrationService->removeSessionData2();

            if (!empty($files["uploadedFile"])){
                $this->foreignDriversLicenseService->saveUploadedFiles(
                    $files["uploadedFile"],
                    $customer
                );
            }
            $this->formSK2->clearRegisteredData();

        } catch (\Exception $e) {
            $this->registrationService->notifySharengoErrorByEmail($e->getMessage() . ' ' . json_encode($e->getTrace()));
            //return $this->redirect()->toRoute('new-signup-2', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('signupSK2', ['mobile' => $mobile]);
        }
        $data["email"] = $customer->getEmail();
        $this->getEventManager()->trigger('secondFormCompleted', $this, $data); //driver license validation
        $signupSession = new Container('newSignup');
        $signupSession->offsetSet("customer", $customer);
        //$this->events->trigger('registeredCustomerPersisted', $this, ['customer' => $customer]);
        return $this->redirect()->toRoute('signup-3', ['mobile' => $mobile], ['query' => ['lang' => 'sk_SK']]);
        //return $this->redirect()->toRoute('area-utente', ['mobile' => $mobile]);

    }





    public function optionalAction(){
        $promocodeMemberGetMember = '';

        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }

        $message = $this->params()->fromQuery('messaggio');
        $outcome = $this->params()->fromQuery('outcome');

        //$customerSession = $this->registrationService->getSignupCustomerSession();    // non deserializza correttamente (_PHP_Incomplete_Class
        $customerSession = $this->customersService->findById($this->params()->fromQuery('c'));

        /* redirect if session is empty */
        if(empty($customerSession)){
            //return $this->redirect()->toRoute('new-signup', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('new-signup', ['mobile' => $mobile]);
        }

        if ($customerSession instanceof Customers){
            $promocodeMemberGetMember = $this->customersService->getPromocodeMemberGetMember($customerSession);
            if($customerSession->getId() != $this->params()->fromQuery('c')){
                //return $this->redirect()->toRoute('new-signup', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
                return $this->redirect()->toRoute('new-signup', ['mobile' => $mobile]);
            }
        }

        //$optionalData = $this->optionalForm->getRegisteredData();

        /*if (!empty($optionalData)) {
            $this->optionalForm->setData([
                'optional' => $optionalData
            ]);
        }*/

        if ($this->getRequest()->isPost()) {

             return $this->redirect()->toRoute('area-utente');
            //return $this->optionalConclude($this->optionalForm, $customerSession, $mobile);

//            $formData = $this->getRequest()->getPost();
//
//            $this->optionalForm->setData($formData);
//            if ($this->optionalForm->isValid()) {
//                //error_log(json_encode($formData));
//                return $this->optionalConclude($this->optionalForm, $customerSession, $mobile);
//            } else {
//                /*foreach ($this->newForm2->getMessages() as $messageId => $message) {
//                    error_log(json_encode($message));
//                }*/
//                return $this->optionalForm($this->optionalForm, $message, $outcome, $mobile, $promocodeMemberGetMember);
//            }
        } else {
            return $this->optionalForm($this->optionalForm, $message, $outcome, $mobile, $promocodeMemberGetMember);
        }
    }

    private function optionalForm($optionalForm, $message, $outcome, $mobile, $promocodeMemberGetMember) {
        if ($mobile) {
            $this->layout('layout/map');
        }

        return new ViewModel([
            'form' => $optionalForm,
            'message' => $message,
            'outcome' => $outcome,
            'mobile' => $mobile,
            'promocodeMemberGetMember' => $promocodeMemberGetMember
        ]);
    }

    private function optionalConclude($form, $customer, $mobile) {
        $form->registerData();

        $data = $this->registrationService->retrieveValidOptionalData();

        // if $data is empty it means that the session expired, so we redirect the user to the beginning of the registration
        if (empty($data)) {
            $message = $this->translator->translate('La sessione è scaduta. E\' necessario ripetere la procedura di registrazione');
            $this->flashMessenger()->addErrorMessage($message);
            //return $this->redirect()->toRoute('optional', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('optional', ['mobile' => $mobile]);
        }
        //$data = $this->registrationService->formatOptionalData($data, $customer);

        try {
            $customer = $this->registrationService->updateOptionalData($data, $customer->getId());
            $this->registrationService->removeSessionOptionalData();

        } catch (\Exception $e) {
            //$this->registrationService->notifySharengoErrorByEmail($e->getMessage() . ' ' . json_encode($e->getTrace()));
            //return $this->redirect()->toRoute('optional', ['lang' => $this->languageService->getLanguage(), 'mobile' => $mobile]);
            return $this->redirect()->toRoute('optional', ['mobile' => $mobile]);
        }

        return $this->redirect()->toRoute('area-utente');
    }

    public function googleMapsCallAction(){
        $a='';
        
        $lon = $this->params()->fromPost('lon');
        $lat = $this->params()->fromPost('lat');
        
        $src = 'https://www.google.it/maps/api/staticmap?center=' .
            $lat . ',' . $lon .
            '&zoom=16&sensor=false&size=800x600&markers=color:green%7C' .
            $lat . ',' . $lon .
            '&key=' . $this->googleMapsConfig['key'];
        
        return new JsonModel([
            'src' => $src
        ]);
        
    }
}
