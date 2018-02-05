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
use SharengoCore\Exception\FleetNotFoundException;
use SharengoCore\Service\CarsService;
use SharengoCore\Service\FleetService;
use SharengoCore\Service\ZonesService;
use SharengoCore\Service\PoisService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Entity\Customers;

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
     * @param string $mobileUrl
     */
    public function __construct(
        $mobileUrl,
        ZonesService $zoneService,
        CarsService $carsService,
        FleetService $fleetService,
        PoisService $poisService,
        CustomersService $customersService
    ) {
        $this->mobileUrl = $mobileUrl;
        $this->zoneService = $zoneService;
        $this->carsService = $carsService;
        $this->fleetService = $fleetService;
        $this->poisService = $poisService;
        $this->customerService = $customersService;
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
