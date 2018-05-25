<?php

namespace Application\Controller;

//use SharengoCore\Entity\CustomersBonus;
//use SharengoCore\Entity\PromoCodes;
use SharengoCore\Entity\CustomerDeactivation;
use SharengoCore\Service\TripsService;
use Application\Form\DriverLicenseForm;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
//use Zend\EventManager\EventManager;
//use Zend\Stdlib\Parameters;
use Zend\Session\Container;
use SharengoCore\Service\CustomersService;
use SharengoCore\Entity\Customers;
use SharengoCore\Service\InvoicesService;
//use SharengoCore\Entity\Invoices;
use SharengoCore\Service\TripPaymentsService;
//use SharengoCore\Exception\BonusAssignmentException;
use SharengoCore\Service\DisableContractService;
use Cartasi\Service\CartasiPaymentsService;
use Cartasi\Service\CartasiContractsService;
use SharengoCore\Service\PaymentScriptRunsService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\CustomerDeactivationService;

class UserAreaController extends AbstractActionController {

    const SUCCESS_MESSAGE = 'Operazione eseguita con successo!';

    /**
     * @var CustomersService
     */
    private $customerService;

    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    private $userService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var Customers
     */
    private $customer;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    /**
     * @var \Zend\Form\Form
     */
    private $profileForm;

    /**
     * @var \Zend\Form\Form
     */
    private $passwordForm;

    /**
     * @var \Zend\Form\Form
     */
    private $mobileForm;

    /**
     * @var \Zend\Form\Form
     */
    private $typeForm;

    /**
     * @var \Zend\Form\Form
     */
    private $driverLicenseForm;

    /**
     * @var \Cartasi\Service\CartasiPaymentsService
     */
    private $cartasiPaymentsService;

    /**
     * @var boolean
     */
    private $showError = false;

    /**
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @var CartasiContractsService
     */
    private $cartasiContractsService;

    /**
     * @var string
     */
    private $bannerJsonpUrl;

    /**
     * @var DisableContractService
     */
    private $disableContractService;

    /**
     * @var PaymentScriptRunsService
     */
    private $paymentScriptRunsService;

    /**
     * @var PaymentsService
     */
    private $paymentsService;

    /**
     * @var CustomerDeactivationService
     */
    private $customerDeactivationService;

    /**
     * @param CustomersService $customerService
     * @param TripsService $tripsService
     * @param AuthenticationService $userService
     * @param InvoicesService $invoicesService
     * @param Form $profileForm
     * @param Form $passwordForm
     * @param Form $mobileForm
     * @param Form $driverLicenseForm
     * @param HydratorInterface $hydrator
     * @param CartasiPaymentsService $cartasiPaymentsService
     * @param TripPaymentsService $tripPaymentsService
     * @param CartasiContractsService $cartasiContractsService
     * @param string $bannerJsonpUrl
     * @param DisableContractService $disableContractService
     * @param PaymentScriptRunsService $paymentScriptRunService
     * @param PaymentService $paymentsService
     * @param CustomerDeactivationService $customerDeactivationService
     */
    public function __construct(
    CustomersService $customerService, TripsService $tripsService, AuthenticationService $userService, InvoicesService $invoicesService, Form $profileForm, Form $passwordForm, Form $mobileForm, Form $driverLicenseForm, HydratorInterface $hydrator, CartasiPaymentsService $cartasiPaymentsService, TripPaymentsService $tripPaymentsService, CartasiContractsService $cartasiContractsService, $bannerJsonpUrl, DisableContractService $disableContractService, PaymentScriptRunsService $paymentScriptRunService, PaymentsService $paymentsService, CustomerDeactivationService $customerDeactivationService
    ) {
        $this->customerService = $customerService;
        $this->tripsService = $tripsService;
        $this->userService = $userService;
        $this->invoicesService = $invoicesService;
        $this->customer = $userService->getIdentity();
        $this->profileForm = $profileForm;
        $this->passwordForm = $passwordForm;
        $this->mobileForm = $mobileForm;
        $this->driverLicenseForm = $driverLicenseForm;
        $this->hydrator = $hydrator;
        $this->cartasiPaymentsService = $cartasiPaymentsService;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->bannerJsonpUrl = $bannerJsonpUrl;
        $this->disableContractService = $disableContractService;
        $this->paymentScriptRunsService = $paymentScriptRunService;
        $this->paymentsService = $paymentsService;
        $this->customerDeactivationService = $customerDeactivationService;
    }

    /**
     * 
     * @return ViewModel
     */
    public function indexAction() {
        //if there is mobile param the layout changes
        $mobile = substr($this->getRequest()->getUriString(), -6);
        $userAreaMobile = '';
        $mobileParam = NULL;
        if ($mobile == 'mobile') {
            $this->layout('layout/map');
            $mobileParam = 'mobile';
            $userAreaMobile = '/' . $mobileParam;
        }

        $customer = $this->userService->getIdentity();

        $redirect = $this->redirectDeactivation($customer, $mobile);
        if ($redirect != null) {
            return $redirect;
        }

        // if not, continue with index action
        $this->setFormsData($this->customer);
        $editForm = true;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            if (isset($postData['customer'])) {
                $postData['customer']['id'] = $this->userService->getIdentity()->getId();

                //prevent gender editing
                $postData['customer']['gender'] = $this->userService->getIdentity()->getGender();

                // ensure vat is not NULL, but a string
                if (is_null($postData['customer']['vat'])) {
                    $postData['customer']['vat'] = '';
                }

                $customerOldTaxCode = $customer->getTaxCode();
                $editForm = $this->processForm($this->profileForm, $postData);
                $this->typeForm = 'edit-profile';

                if ($postData['customer']['taxCode'] != $customerOldTaxCode) {
                    // if we change the tax code we need to revalidate the driver's license
                    $params = [
                        'email' => $postData['customer']['email'],
                        'driverLicense' => $customer->getDriverLicense(),
                        'taxCode' => $postData['customer']['taxCode'],
                        'driverLicenseName' => $customer->getDriverLicenseName(),
                        'driverLicenseSurname' => $customer->getDriverLicenseSurname(),
                        'birthDate' => ['date' => date_create($postData['customer']['birthDate'])->format('Y-m-d')],
                        'birthCountry' => $postData['customer']['birthCountry'],
                        'birthProvince' => $postData['customer']['birthProvince'],
                        'birthTown' => $postData['customer']['birthTown']
                    ];

                    $this->getEventManager()->trigger('taxCodeEdited', $this, $params);
                }
            } else if (isset($postData['password'])) {
                $postData['id'] = $this->userService->getIdentity()->getId();
                $editForm = $this->processForm($this->passwordForm, $postData);
                $this->typeForm = 'edit-pwd';
            } else
            if (isset($postData['mobile'])) {
                $postData['id'] = $this->userService->getIdentity()->getId();

                if ($customer->getMobile() == $postData['mobile'] && $postData['smsCode'] == "") {
                    $postData['smsCode'] = "0000";
                }
                $editForm = $this->processForm($this->mobileForm, $postData);
                $postData['smsCode'] = "";
                $this->typeForm = 'edit-mobile';

                //unset the session
                $smsVerification = new Container('smsVerification');
                //$smsVerification->offsetUnset('mobile');
                $smsVerification->offsetUnset('code');
            }

            if ($editForm) {
                return $this->redirect()->toRoute('area-utente' . $userAreaMobile);
            }
        }

        return new ViewModel([
            'bannerJsonpUrl' => $this->bannerJsonpUrl,
            'customer' => $this->customer,
            'profileForm' => $this->profileForm,
            'passwordForm' => $this->passwordForm,
            'mobileForm' => $this->mobileForm,
            'showError' => $this->showError,
            'typeForm' => $this->typeForm,
        ]);
    }

    private function processForm($form, $data) {
        $form->setData($data);
        if ($form->isValid()) {
            $customer = $form->saveData();

            //update the data in the form
            $this->setFormsData($customer);

            //update the data in the view
            $this->customer = $customer;

            $this->flashMessenger()->addSuccessMessage(self::SUCCESS_MESSAGE);

            return true;
        }

        $this->showError = true;

        return false;
    }

    private function setFormsData(Customers $customer) {
        $customerData = $this->hydrator->extract($customer);
        $this->profileForm->setData(['customer' => $customerData]);
    }

    public function ratesAction() {
        //if there is mobile param the layout changes
        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }
        $customer = $this->identity();

        if (!$customer instanceof Customers) {
            return $this->response->setStatusCode(403);
        }

        return new ViewModel([
            'customer' => $this->customer
        ]);
    }

    public function ratesConfirmAction() {
        $option = $this->params()->fromPost('option');

        $customer = $this->customerService->setCustomerReprofilingOption($this->customer, $option);

        return new JsonModel([
            'option' => $option
        ]);
    }

    public function pinAction() {
        return new ViewModel();
    }

    public function datiPagamentoAction() {
        //if there is mobile param the layout changes
        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }
        $customer = $this->userService->getIdentity();
        $activateLink = $this->customerService->isFirstTripManualPaymentNeeded($customer);
        $cartasiCompletedFirstPayment = $this->cartasiPaymentsService->customerCompletedFirstPayment($customer);
        $contract = $this->cartasiContractsService->getCartasiContract($customer);
        $tripPayment = $this->tripPaymentsService->getFirstTripPaymentNotPayedByCustomer($customer);

        return new ViewModel([
            'customer' => $customer,
            'cartasiCompletedFirstPayment' => $cartasiCompletedFirstPayment,
            'contract' => $contract,
            'activateLink' => $activateLink
        ]);
    }

    public function tripsAction() {
        return new ViewModel([
            'trips' => $this->tripsService->getTripsByCustomer($this->customer->getId())
        ]);
    }

    public function drivingLicenceAction() {
        //if there is mobile param the layout changes
        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }
        $customer = $this->userService->getIdentity();

        if ($this->redirectRegistrationNotCompleted($customer)){
            return $this->redirect()->toUrl($this->url()->fromRoute('new-signup-2', ['mobile' => $mobile]));
        }

        /** @var DriverLicenseForm $form */
        $form = $this->driverLicenseForm;
        $customerData = $this->hydrator->extract($this->customer);
        $form->setData(['driver' => $customerData]);
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $postData['driver']['id'] = $customer->getId();
            if (!isset($postData['driver']['driverLicenseCategories'])) {
                $driver = $postData['driver'];
                $driver['driverLicenseCategories'] = [];
                $postData['driver'] = $driver;
            }
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $this->customerService->saveDriverLicense($form->getData());

                    $params = [
                        'email' => $customer->getEmail(),
                        'driverLicense' => $customer->getDriverLicense(),
                        'taxCode' => $customer->getTaxCode(),
                        'driverLicenseName' => $customer->getDriverLicenseName(),
                        'driverLicenseSurname' => $customer->getDriverLicenseSurname(),
                        'birthDate' => ['date' => $customer->getBirthDate()->format('Y-m-d')],
                        'birthCountry' => $customer->getBirthCountry(),
                        'birthProvince' => $customer->getBirthProvince(),
                        'birthTown' => $customer->getBirthTown()
                    ];

                    $this->getEventManager()->trigger('driversLicenseEdited', $this, $params);

                    $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. Ci scusiamo per l\'inconveniente');
                }

                return $this->redirect()->toRoute('area-utente/patente', ['mobile' => $mobile]);
            } else {
                $this->showError = true;
            }
        }

        $driversLicenseUpload = $this->customerService->customerNeedsToAcceptDriversLicenseForm($this->customer) &&
                !$this->customerService->customerHasAcceptedDriversLicenseForm($this->customer);

        return new ViewModel([
            'customer' => $this->customer,
            'driverLicenseForm' => $form,
            'showError' => $this->showError,
            'driversLicenseUpload' => $driversLicenseUpload
        ]);
    }

    public function bonusAction() {
        //if there is mobile param the layout changes
        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }
        return new ViewModel([
            'customer' => $this->customer,
            'listBonus' => $this->customerService->getAllBonus($this->customer)
        ]);
    }

    public function invoicesListAction() {
        //if there is mobile param the layout changes
        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }

        $customer = $this->userService->getIdentity();
        $availableDates = $this->invoicesService->getDistinctDatesForCustomerByMonth($customer);

        return new ViewModel(
                ['availableDates' => $availableDates]
        );
    }

    public function rentsAction() {
        $customer = $this->userService->getIdentity();
        $availableDates = $this->tripsService->getDistinctDatesForCustomerByMonth($customer);

        return new ViewModel(
                ['availableDates' => $availableDates]
        );
    }

    public function debtCollectionAction() {
        //if there is mobile param the layout changes
        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }
        $customer = $this->userService->getIdentity();

        $tripsToBePayedAndWrong = null;
        $totalCost = $this->customerService->getTripsToBePayedAndWrong($customer, $tripsToBePayedAndWrong);

        $contract = $this->cartasiContractsService->getCartasiContract($customer);

        $tripPayment = $this->tripPaymentsService->getFirstTripPaymentNotPayedByCustomer($customer);
        $scriptIsRunning = $this->paymentScriptRunsService->isRunning();

        return new ViewModel([
            'customer' => $customer,
            'contract' => $contract,
            'tripPayment' => $tripPayment,
            'tripsToBePayedAndWrong' => $tripsToBePayedAndWrong,
            'totalCost' => $totalCost,
            'scriptIsRunning' => $scriptIsRunning
        ]);
    }

    public function debtCollectionPaymentAction() {
        //if there is mobile param the layout changes
        $mobile = $this->params()->fromRoute('mobile');
        $userAreaMobile = '';
        if ($mobile) {
            $this->layout('layout/map');
            $userAreaMobile = '/' . $mobile;
        }
        $scriptIsRunning = $this->paymentScriptRunsService->isRunning();

        if (!$scriptIsRunning) {
            $trips = null;
            $customer = $this->userService->getIdentity();
            $totalCost = $this->customerService->getTripsToBePayedAndWrong($customer, $trips);
            if ($totalCost > 0) {
                if ($this->cartasiContractsService->hasCartasiContract($customer)) {
                    $response = $this->paymentsService->tryTripPaymentMulti($customer, $trips);
                    if ($response->getCompletedCorrectly()) {
                        $this->flashMessenger()->addSuccessMessage('Pagamento completato con successo');
                    } else {
                        $this->flashMessenger()->addErrorMessage('Pagamento fallito');
                    }
                } else {
                    return $this->redirect()->toRoute('cartasi/primo-pagamento-corsa-multi', [], ['query' => ['customer' => $customer->getId()]]);
                }
            } else {
                return $this->redirect()->toUrl($this->url()->fromRoute('area-utente' . $userAreaMobile));
            }
        } else {
            $this->flashMessenger()->addErrorMessage('Pagamento momentaneamente sospeso, riprova più tardi.');
        }

        return $this->redirect()->toUrl($this->url()->fromRoute('area-utente/debt-collection', ['mobile' => $mobile]));
    }

    public function packageMySharengoAction() {
        //if there is mobile param change layout
        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }
        return new ViewModel();
    }

    public function paymentSecurecodeCartasiAction() {
        return new ViewModel();
    }

    public function disableContractAction() {
        $customer = $this->userService->getIdentity();
        $contractId = $this->getRequest()->getQuery("contractId", null);

        if (isset($contractId)) {
            $contract = $this->cartasiContractsService->getContractById($contractId);
            if ($contract->getCustomerId() === $customer->getId()) {
                try {
                    $this->disableContractService->disableContract($contract);

                    $this->flashMessenger()->addSuccessMessage('Contratto disabilitato correttamente!');
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Errore durante la disabilitazione del contratto');
                }
            } else {
                $this->flashMessenger()->addErrorMessage('Errore durante la disabilitazione del contratto');
            }
        } else {
            $this->flashMessenger()->addErrorMessage('Errore durante la disabilitazione del contratto');
        }

        return $this->redirect()->toRoute('area-utente/dati-pagamento');
    }

    public function maintenancePageAction() {
        return new ViewModel();
    }

    private function redirectDeactivation(Customers $customer, $mobile){
        $userAreaMobile = '';

        $mobileParam = NULL;
        if ($mobile == 'mobile') {
            $this->layout('layout/map');
            $mobileParam = 'mobile';
            $userAreaMobile = '/' . $mobileParam;
        }

        if ($this->redirectRegistrationNotCompleted($customer)){
            return $this->redirect()->toUrl($this->url()->fromRoute('new-signup-2', ['mobile' => $mobileParam]));
        }

        $deactivations = $this->customerDeactivationService->getAllActive($customer);
        if(count($deactivations) > 0){
            foreach($deactivations as $deactivation){
                switch ($deactivation->getReason()) {
                    case CustomerDeactivation::FIRST_PAYMENT_NOT_COMPLETED:
                        return $this->redirect()->toUrl($this->url()->fromRoute('area-utente/debt-collection', ['mobile' => $mobileParam]));
                        break;
                    case CustomerDeactivation::FAILED_PAYMENT:
                        return $this->redirect()->toUrl($this->url()->fromRoute('area-utente/debt-collection', ['mobile' => $mobileParam]));
                        break;
                    case CustomerDeactivation::EXPIRED_CREDIT_CARD:
                        $this->flashMessenger()->addErrorMessage('Sei disabilitato perchè la carta inserita è scaduta, inserisci i nuovi dati.');
                        return $this->redirect()->toUrl($this->url()->fromRoute('area-utente/debt-collection', ['mobile' => $mobileParam]));
                        break;
                    case CustomerDeactivation::INVALID_DRIVERS_LICENSE:
                        $this->flashMessenger()->addErrorMessage('Sei disabilitato perchè hai inserito una patente non valida, controlla e modifica i dati inseriti.');
                            return $this->redirect()->toUrl($this->url()->fromRoute('area-utente/patente', ['mobile' => $mobileParam]));
                        break;
                    case CustomerDeactivation::EXPIRED_DRIVERS_LICENSE:
                        $this->flashMessenger()->addErrorMessage('Sei disabilitato per patente scaduta, inserisci i nuovi dati.');
                            return $this->redirect()->toUrl($this->url()->fromRoute('area-utente/patente', ['mobile' => $mobileParam]));
                        break;
                    /*case 'DISABLED_BY_WEBUSER':
                        return 'Disabilitato manualmente';
                        break;*/
                }
            }
            //DOUBLE CHECK FOR FAILED TRIP PAYMENT & FIRST PAYMENT NOT COMPLETED
            if ($this->tripsService->getTripsToBePayedAndWrong($customer, $paymentsToBePayedAndWrong) > 0 ||
                (!$customer->getEnabled() && !$customer->getFirstPaymentCompleted())) {
                return $this->redirect()->toUrl($this->url()->fromRoute('area-utente/debt-collection', ['mobile' => $mobileParam]));
            }
        }

    }

    private function redirectRegistrationNotCompleted($customer)
    {
        $registrationNotCompleted = $this->customerDeactivationService->getAllActive($customer, CustomerDeactivation::REGISTRATION_NOT_COMPLETED);
        if (is_null($registrationNotCompleted)) {
            return false;
        } else {
            $signupSession = new Container('newSignup');
            $signupSession->offsetSet("customer", $customer);
            return true;
        }
    }

}
