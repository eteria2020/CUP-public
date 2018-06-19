<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

// Internals
use Application\Service\RegistrationService;
use SharengoCore\Exception\FleetNotFoundException;
use SharengoCore\Service\CarsService;
use SharengoCore\Service\FleetService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\ZonesService;
use SharengoCore\Service\PoisService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\PaymentScriptRunsService;
use SharengoCore\Entity\Customers;
use Cartasi\Service\CartasiContractsService;
use Cartasi\Entity\Contracts;

// Externals
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{

    /**
     * @var string
     */
    private $mobileUrl;

    /**
     * @var ZoneService
     */
    private $zoneService;

    /**
     * @var CarsService
     */
    private $carsService;

    /**
     * @var FleetService
     */
    private $fleetService;

    /**
     * @var PoisService
     */
    private $poisService;

    /**
     * @var CustomersService
     */
    private $customerService;

    /**
     * @var CartasiContractsService
     */
    private $cartasiContractsService;

    /**
     * @var CartasiContractsService
     */
    private $registrationService;

    /**
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @var PaymentsService
     */
    private $paymentsService;

    /**
     * @var PaymentScriptRunsService
     */
    private $paymentScriptRunsService;

    /**
     * @var TripsService
     */
    private $tripsService;



    public function __construct(
        $mobileUrl,
        ZonesService $zoneService,
        CarsService $carsService,
        FleetService $fleetService,
        PoisService $poisService,
        CustomersService $customersService,
        CartasiContractsService $cartasiContractsService,
        RegistrationService $registrationService,
        TripPaymentsService $tripPaymentsService,
        PaymentScriptRunsService $paymentScriptRunsService,
        PaymentsService $paymentsService,
        TripsService $tripsService
    ) {
        $this->mobileUrl = $mobileUrl;
        $this->zoneService = $zoneService;
        $this->carsService = $carsService;
        $this->fleetService = $fleetService;
        $this->poisService = $poisService;
        $this->customerService = $customersService;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->registrationService = $registrationService;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->paymentScriptRunsService = $paymentScriptRunsService;
        $this->paymentsService = $paymentsService;
        $this->tripsService = $tripsService;
    }

    public function indexAction()
    {
        // Any mobile device (phones or tablets).
        /*if ($this->mobileDetect()->isMobile()) {
            $this->redirect()->toUrl($this->mobileUrl);
        }*/

        return new ViewModel();
    }

    public function map2Action()
    {
        $this->layout('layout/map2');
        return new ViewModel();
    }

    public function mapAction()
    {   
        return new ViewModel();
    }
    /**
     * @return \Zend\Http\Response (JSON Format)
     */
    public function getListZonesAction()
    {
        $data = $this->zoneService->getListZones(false, true);

        /** @var array $zone */
        foreach ($data as $zone) {
            $data[$zone['id']] = json_decode($zone['areaUse']);
        }

        $this->getResponse()->setContent(json_encode($data));
        return $this->getResponse();
    }

    public function getListOsmZonesAction()
    {
        $data = $this->zoneService->getListZones(false, true);

        $this->getResponse()->setContent(json_encode($data));
        return $this->getResponse();
    }
    
    public function getListCarsByFleetAction()
    {
        $fleetId = $this->params()->fromRoute('fleetId', 0);

        try {
            $fleet = $this->fleetService->getFleetById($fleetId);
        } catch (FleetNotFoundException $exception) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        return new JsonModel(
            $this->carsService->getPublicFreeCarsByFleet($fleet)
        );
    }
    
    public function getListCarsByFleetApiAction()
    {
        $fleetId = $this->params()->fromRoute('fleetId', 0);
        //$lon=9.192831;
        //$lat=45.465718;
        $radius=40000;
        try {
            $fleet = $this->fleetService->getFleetById($fleetId);
        } catch (FleetNotFoundException $exception) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }
        
        $lat = $fleet->getLatitude();
        $lon = $fleet->getLongitude();
        
        try {
            $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 40
                    )
                )
            );
            $link = "http://localhost:8021/v3/cars?lat=".$lat."&lon=".$lon."&radius=".$radius;
            $data = shell_exec("curl '".$link."'");
            $data = json_decode($data, true);
            $data = $data['data'];
            //$this->getResponse()->setContent(json_encode($data));
            return new JsonModel($data);
        } catch (Exception $exception) {
            return "{}";
        }
    }

    public function getListPoisByFleetAction()
    {
        $fleetId = $this->params()->fromRoute('fleetId', 0);

        try {
            $fleet = $this->fleetService->getFleetById($fleetId);
        } catch (FleetNotFoundException $exception) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return false;
        }

        return new JsonModel(
            $this->poisService->getPublicPoisByFleet($fleet)
        );
    }


    public function carsharingAction()
    {
        $view = new ViewModel();
        $view->setTemplate('application/index/index');

        return $view;
    }

    public function cosaeAction()
    {
        return new ViewModel();
    }

    public function quantocostaAction()
    {
        return new ViewModel();
    }

    public function comefunzionaAction()
    {
        return new ViewModel();
    }

    public function faqAction()
    {
        $this->redirect()->toUrl('http://support.sharengo.it');
    }

    public function contattiAction()
    {
        return new ViewModel();
    }

    public function cookiesAction()
    {
        return new ViewModel();
    }

    public function notelegaliAction()
    {
        return new ViewModel();
    }

    public function privacyAction()
    {
        return new ViewModel();
    }

    public function callcenterAction()
    {
        return new ViewModel();
    }

    public function eqSharingAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function bikemiAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function teatroElfoAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function appredAction()
    {
        $plate = $this->params()->fromRoute('plate');
        $this->redirect()->toUrl("http://mobile.sharengo.it/index.php?plate=$plate");
    }

    public function rescueCodeAction(){
        $userId = $this->params()->fromRoute('userId');

        $customer = $this->customerService->findById($userId);

        if(!$customer instanceof Customers) {
            return $this->notFoundAction();
        }
        if ($customer->getEnabled() == true){
            return $this->notFoundAction();
        }
        $this->redirect()->toUrl("https://www.sharengo.it/cartasi/primo-pagamento?customer=$userId");

    }

    public function registrationCompletedAction(){
        $userId = $this->params()->fromRoute('userId');

        $customer = $this->customerService->findById($userId);

        if(!$customer instanceof Customers) {
            return $this->notFoundAction();
        }
        if ($customer->getEnabled() == true){
            return $this->notFoundAction();
        }
        //update the registration_completed field -> TRUE
        $this->registrationService->registerUser($customer->getHash());

        $this->redirect()->toUrl("https://www.sharengo.it/cartasi/primo-pagamento?customer=$userId");

    }

    public function expiredCreditCardAction(){
        $userId = $this->params()->fromRoute('userId');

        $customer = $this->customerService->findById($userId);

        if(!$customer instanceof Customers) {
            return $this->notFoundAction();
        }
        if (!$customer->getEnabled()){
            return $this->notFoundAction();
        }

        $contract = $this->cartasiContractsService->getCartasiContract($customer);

        if (is_null($contract)) {
            $contract = 'no_contract';
        } else if($contract instanceof  Contracts){
            $contract = $contract->getId();
        }

        $url = "https://www.sharengo.it/cartasi/cambio-carta?customer=".$customer->getId()."&contract=$contract";
        $this->redirect()->toUrl($url);

    }

    public function outstandingPaymentsAction(){
        $userId = $this->params()->fromRoute('userId');

        $customer = $this->customerService->findById($userId);

        if(!$customer instanceof Customers) {
            return $this->notFoundAction();
        }

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

    public function outstandingPaymentsDoAction() {

        $userId = $this->params()->fromRoute('userId');

        $customer = $this->customerService->findById($userId);

        if(!$customer instanceof Customers) {
            $this->flashMessenger()->addErrorMessage('Pagamento momentaneamente sospeso, riprova più tardi.');
            return $this->redirect()->toUrl($this->url()->fromRoute('outstanding', ['userId' => $userId]));
        }

        $scriptIsRunning = $this->paymentScriptRunsService->isRunning();

        if (!$scriptIsRunning) {
            $trips = null;
            $totalCost = $this->customerService->getTripsToBePayedAndWrong($customer, $trips);
            if ($totalCost > 0) {
                /*if ($this->cartasiContractsService->hasCartasiContract($customer)) {
                    $response = $this->paymentsService->tryTripPaymentMulti($customer, $trips);
                    if ($response->getCompletedCorrectly()) {
                        $this->flashMessenger()->addSuccessMessage('Pagamento completato con successo');
                    } else {
                        $this->flashMessenger()->addErrorMessage('Pagamento fallito');
                    }
                } else {*/
                    return $this->redirect()->toRoute('cartasi/primo-pagamento-corsa-multi', [], ['query' => ['userId' => $customer->getId()]]);
               // }
            } else {
                return $this->redirect()->toUrl($this->url()->fromRoute('outstandingPayments', ['userId' => $userId]));
            }
        } else {
            $this->flashMessenger()->addErrorMessage('Pagamento momentaneamente sospeso, riprova più tardi.');
        }

        return $this->redirect()->toUrl($this->url()->fromRoute('outstandingPayments', ['userId' => $userId]));
    }

    public function bannerAction(){
        $customerId = $this->params()->fromQuery('id', '');
        $callback = $this->params()->fromQuery('callback', '');
        $link = $this->params()->fromQuery('link', '');

        $resp = "";
        $this->getResponse()->setContent($resp);

        if(intval($customerId) <= 0){
            return $this->getResponse();
        }

        if(end(explode('/',$link)) != "area-utente"){
            return $this->getResponse();
        }

        if($callback != "setBanner"){
            return $this->getResponse();
        }

        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://manage.sharengo.it/banner.php?id='.$customerId.'&callback='.$callback.'&link='.$link,
            CURLOPT_USERAGENT => 'Sharengo Public Banner'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        //$resp = $customerId.$callback.$link;
        $this->getResponse()->setContent($resp);
        return $this->getResponse();
    }


}
