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


// Internal Modules
use Application\Service\RegistrationService;
use Multilanguage\Service\LanguageService;
use Application\Service\ProfilingPlaformService;
use Application\Exception\ProfilingPlatformException;
use Application\Form\RegistrationForm;
use SharengoCore\Service\CustomersService;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\Fleet;

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
    //$this->smsVerificationCode=new Zend_Session_Namespace('smsVerification');
             

    Form $form1, Form $form2, RegistrationService $registrationService, CustomersService $customersService, LanguageService $languageService, ProfilingPlaformService $profilingPlatformService, Translator $translator, HydratorInterface $hydrator
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

    public function loginAction() {
        return new ViewModel();
    }

    public function signupAction() {
        
        //if(!$smsVerification->offsetExists($key)){
         //   $smsVerification=new Container('smsVerification');
         //   $smsVerification->offsetSet('timeStamp', new \DateTime());
        //}
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
            
            /*
            $smsCode=$formData['user']['smsCode'];
            if($smsCode!="" && $smsCode!=null){
                $resultVerification=$this->signupVerifyCodeAction($smsCode);
            }
            $risultato=$resultVerification->getContent();
            */
            /*&& $risultato*/
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
        //$smsVerification->code=$_SESSION["codice"]; 
        $smsVerification=new Container('smsVerification');
        //$insertedCode=$this->params()->fromPost('codiceUtenteSms');
        if($smsVerification->offsetGet('code')==$smsCode){
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(true);
            return $response;
            /*$this->response->setStatusCode(200);
            return new JsonModel(array("message" => "COD ESATTO!"));*/
        }else{
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(false);
            return $response;
            /*$this->response->setStatusCode(200);
            return new JsonModel(array("message" => "COD ERRATO!"));*/
        }
       
    }

    public function signupSmsAction() {
     //$mex=$this->generateTextMex();
     //$_SESSION["time"];   
     //$session = new Container('userDiscount');
         $smsVerification=new Container('smsVerification');
        
        
     if (!$smsVerification->offsetExists('timeStamp')){
          //$ora=new \DateTime();
          //$session->offSet()
          //$session->offsetSet('time', $ora);
          //$code=$this->codeGenerator();
          //$_SESSION["codice"]=$code;
          //$phone=$_POST["telefono"];
          //$_SESSION["cell"] = $phone;
          $smsVerification->offsetSet('timeStamp', new \DateTime());
          $smsVerification->offsetSet('mobile',$_POST["mobile"]);
          $smsVerification->offsetSet('code',$this->codeGenerator()) ;
          $this->gestioneInvioSms($smsVerification->offsetGet('mobile'),$smsVerification->offsetGet('code'));
          $response = $this->getResponse();
          $response->setStatusCode(200);
          $response->setContent("SMS Inviato iniziale");
          return $response;
     }else{
          /*$now= date("h:i:sa");
          $sessionStart=$session->offsetGet('time');
          $diffSeconds = $now->getTimestamp() - $sessionStart->getTimestamp();*/
          //$s=$session->offsetGet('time');
          $now = new \DateTime();
          $diffSeconds = $now->getTimestamp()-$smsVerification->offsetGet('timeStamp')->getTimeStamp() ;
         if($diffSeconds>60){
              //$smsVerification->code=$this->codeGenerator();
              //$_SESSION["codice"]=$code;
              //$ora=new \DateTime();
              //$session->offsetSet('time', $ora);
              //$this->gestioneInvioSms($_SESSION["cell"],$_SESSION["codice"]);
              $smsVerification->offsetSet('timeStamp', new \DateTime());
              //in caso sbagliasse numero aggiorno il numero di telefono
              $smsVerification->offsetSet('mobile',$_POST["mobile"]);
              $smsVerification->offsetSet('code',$this->codeGenerator()) ;
              $this->gestioneInvioSms($smsVerification->offsetGet('mobile'),$smsVerification->offsetGet('code'));
              $response = $this->getResponse();
              $response->setStatusCode(200);
              $response->setContent("SMS Inviato dopo tot tempo");
              return $response;
        }else{
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent("Attendere messaggio");
            return $response;
             //attendere messaggio
         }
     }
    
     //$completeMex=$mex.": ".$code;
    }
    
    private function gestioneInvioSms($phone,$code){
         //invio sms
                $username = 'SMSHY8YFB8Z1JHFFQD139';
                $password = 'YHFODFXUGD9IE04U1PK0PIDKZ76SVFXO';
                //$rnd = mt_rand(1000, 9999);
                

                $url = "https://api.smshosting.it/rest/api/sms/send";
                $fields = array(
                        'sandbox' => 'true',
                        //'sandbox' => null,
                        //'to' => "393407924757",
                        'to' => $phone,
                        'from' => "ShareNGO",
                        'text' => utf8_encode("Codice di Verifica $code")
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
                //print "params: " . $postvars;
                //print "error:" . curl_error($ch) . "<br />";
                //print "output:" . $out . "<br /><br />";
                curl_close($ch);

                //fine invio sms
    }//fine gestione invio
    
    private function generateTextMex(){
        $testo="Gentile cliente ecco il codice di conferma per la registrazione";
        return $testo;
    }//fine genera testo
    
    private function codeGenerator(){
     $codice = mt_rand(1000, 9999);
     return $codice;
    }//fine genera codice
    
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
