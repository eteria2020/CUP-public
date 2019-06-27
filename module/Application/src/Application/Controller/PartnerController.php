<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use SharengoCore\Service\SimpleLoggerService;
use SharengoCore\Service\PartnerService;
use SharengoCore\Service\SmsService;

class PartnerController extends AbstractActionController {

    /**
     *
     * @var type 
     */
    private $loggerService;

    /**
     *
     * @var PartnerService partnerService
     */
    private $partnerService;

    /**
     * @var SmsService smsService
     */
    private $smsService;

    /**
     * PartnerController constructor.
     * @param SimpleLoggerService $loggerService
     * @param PartnerService $partnerService
     * @param SmsService $smsService
     */
    public function __construct(
        SimpleLoggerService $loggerService,
        PartnerService $partnerService,
        SmsService $smsService
    ) {
        $this->loggerService = $loggerService;
        $this->partnerService = $partnerService;
        $this->smsService =$smsService;
    }

    /**
     * API for FREE2MOVE partner 
     * https://en.wikipedia.org/wiki/UTM_parameters
     * .../partner?utm_source=free2move
     * This method is a API that return Response obj of zf2.
     * Return two json:
     * - first json contain the number of customer who are leads and the number of customer who are sign in with promocode like "F2MOVE"
     * - second json contain the number of customer who are leads and the number of customer who are sign in with promocode like "F2MAPR"
     * 
     * @return Response
     */
    public function getInfoAction() {

        if (isset($_GET["utm_source"])) {
            if (strtoupper($_GET["utm_source"]) == 'FREE2MOVE') {

                $param = "2MOVE";
                $response_msg = $this->partnerData($param);
                $values1 = array_values(array_map('intval', explode(",", $response_msg)));
                $values1 = json_encode(array_combine(array("lead", "F2MOVE"), $values1));
                
                $param = "2MAPR";
                $response_msg = $this->partnerData($param);
                $values2 = array_values(array_map('intval', explode(",", $response_msg)));
                $values2 = json_encode(array_combine(array("lead", "F2MAPR"), $values2));
                
                $output = $values1 . $values2;

                $response = $this->getResponse();
                $response->setStatusCode(200);
                $response->setContent($output);
                return $response;
            } else {
                $response = $this->getResponse();
                $response->setStatusCode(404);
                return $response;
            }
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            return $response;
        }
    }

    private function partnerData($param) {
        return $this->partnerService->partnerData($param);
    }

    /**
     * Signup action for partners.
     * Receive a POST request whit the customer data in json format and give response.
     *
     * TODO
     * - eliminare campo debug nella response
     * - rimuovere funzione di test
     *
     * @return Request
     */
    public function signupAction() {
        $statusCode = 404;  //404 Not Found
        $partner = null;
        $partnerResponse = null;

        $response = $this->getResponse();
        $response->setStatusCode($statusCode);

        try {

            if ($this->getRequest()->isPost()) {
                //$authorization = $this->getRequest()->getHeader('Authorization', '');
 
                $content = file_get_contents('php://input');
                $this->logger("signupAction;request", $content);
                $contentArray = json_decode($content, true);

                if(!is_null($contentArray)) {
                    //$this->userEventsService->saveNewEvent($webUser,  "customer-partner", $contentArray);     //TODO: to implement
                    //$contentObject = json_decode($content);
                    //$debug=$contentArray['partnerName'];
                    $partnerCode = $this->partnerService->getPartnerCode($contentArray, 'partnerName');
                    $partner = $this->partnerService->findEnabledByCode($partnerCode);

                    if(!is_null($partner)) {
                        $statusCode = $this->partnerService->signup($partner, $contentArray, $partnerResponse);
                        if (!is_null($partnerResponse)) {
                            $response->setStatusCode($statusCode);
                            $response->setContent(json_encode($partnerResponse));
                        }
                    } else {
                        $response->setStatusCode(403);  // 403 Forbidden
                    }
                } else {
                    $response->setStatusCode(400);  // 400 Bad Request
                }
            } else {
                $response->setStatusCode(405);  // 405 Method Not Allowed
            }
        } catch (\Exception $ex) {
            $response->setStatusCode(500);  //500 Internal Server Error
        }

        $this->logger("signupAction:response", $response->getStatusCode().";".$response->getBody());
        return $response;
    }

    /**
     * Notify the customer status of customer belogn to a partner
     * 
     * Function call from console
     */
    public function notifyCustomerStatusAction() {
        $this->loggerService->setOutputEnvironment(SimpleLoggerService::OUTPUT_ON);
        $this->loggerService->setOutputType(SimpleLoggerService::TYPE_CONSOLE);

        $this->loggerService->log(date_create()->format('y-m-d H:i:s').";INF;notifyCustomerStatusAction;start\n");

        $partnerCode = $this->request->getParam('partner');
        $partner = $this->partnerService->findEnabledByCode($partnerCode);
        $this->loggerService->log(date_create()->format('y-m-d H:i:s').";INF;notifyCustomerStatusAction;partner;".$partner->getCode()."\n");

        $this->partnerService->notifyCustomerStatus($partner);
        $this->loggerService->log(date_create()->format('y-m-d H:i:s').";INF;notifyCustomerStatusAction;stop\n");

    }

    /**
     * Import the invoice data form a partner api.
     * 
     * Function call from console
     */
    public function importInvoiceAction() {
        $this->loggerService->setOutputEnvironment(SimpleLoggerService::OUTPUT_ON);
        $this->loggerService->setOutputType(SimpleLoggerService::TYPE_CONSOLE);
        $this->loggerService->log(date_create()->format('y-m-d H:i:s').";INF;importInvoiceAction;start\n");

        $dryRun = $this->request->getParam('dry-run') || $this->request->getParam('d');
        $partnerCode = $this->request->getParam('partner');
        $date = $this->request->getParam('date');
        $fleetId = $this->request->getParam('fleetId');

        if(!is_null($date)) {
            $date = date_create_from_format('Y-m-d', $date);
        } else {
            $date = date_create('yesterday');
        }

        $partner = $this->partnerService->findEnabledByCode($partnerCode);

        if(!is_null($partner)){
            $this->partnerService->importInvoice($dryRun, $partner, $date, $fleetId);
        }

        $this->loggerService->log(date_create()->format('y-m-d H:i:s').";INF;importInvoiceAction;end\n");
    }

     public function exportRegistriesAction() {
        $this->loggerService->setOutputEnvironment(SimpleLoggerService::OUTPUT_ON);
        $this->loggerService->setOutputType(SimpleLoggerService::TYPE_CONSOLE);
        $this->loggerService->log(date_create()->format('y-m-d H:i:s').";INF;exportRegistriesAction;start\n");

        $dryRun = $this->request->getParam('dry-run') || $this->request->getParam('d');
        $noFtp  = $this->request->getParam('no-ftp')  || $this->request->getParam('f');

        $partnerCode = $this->request->getParam('partner');
        $invoiceDate = $this->request->getParam('date');
        $fleetId = $this->request->getParam('fleet');

        $partner = $this->partnerService->findEnabledByCode($partnerCode);
        $this->loggerService->log(date_create()->format('y-m-d H:i:s').";INF;exportRegistriesAction;partner;".$partner->getCode()."\n");

        $this->partnerService->exportRegistries($dryRun, $noFtp, $partner, $invoiceDate, $fleetId);

        $this->loggerService->log(date_create()->format('y-m-d H:i:s').";INF;exportRegistriesAction;end\n");
     }

    /**
     * Write a message with the address (ip) of remote request
     *
     * @param string $info
     * @param string $message
     */
    private function logger($info, $message) {
        try {
            $writer = new \Zend\Log\Writer\Stream("/tmp/partner.log");
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(";" . $this->partnerService->getRemoteAddress() .";" . $info . "\n" . $message);
        } catch (Exception $ex) {

        }
    }

    public function tryChargeAccountTestAction() {

        $culrResponse = null;
        $jsonResponse = null;
        $this->partnerService->tryChargeAccountTest($culrResponse, $jsonResponse);
    }

    /**
     * Trasform a request of SOS from Node Js CarWebServices (restFunctions.js), into a  group of SMS send to the
     * logistic operators
     *
     * @return \Zend\Stdlib\ResponseInterface|null
     */
    public function sosSmsAction() {

        $statusCode = 200;
        $response = null;
        $response = $this->getResponse();
        $params = $this->params()->fromQuery();
        $smsResponse = array();

        $this->logger("sosSmsAction;request", json_encode($params));

        if(PartnerService::isRemoteAddressValid($this->smsService->getValidIpFromConfigDb())) {
            if(isset($params['trip_id'])) {
                $this->smsService->sendSosViaSms($params['trip_id'], $smsResponse);
            }
        } else {
            $smsResponse['result'] = false;
            $smsResponse['error'] = "forbidden for " . PartnerService::getRemoteAddress();
            $statusCode = 403;
        }

        $this->logger("sosSmsAction;response", json_encode($smsResponse));
        $response->setContent(PartnerService::removeUtf8Bom(json_encode($smsResponse)));
        $response->setStatusCode($statusCode);
        return $response;
    }




}
