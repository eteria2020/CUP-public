<?php

namespace Application\Controller;

// External Modules
use Zend\Mvc\Controller\AbstractActionController;
use SharengoCore\Service\SimpleLoggerService;
use SharengoCore\Service\PartnerService;

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
     * 
     * @param SimpleLoggerService $loggerService
     * @param PartnerService $partnerService
     */
    public function __construct(
        SimpleLoggerService $loggerService,
        PartnerService $partnerService
    ) {
        $this->loggerService = $loggerService;
        $this->partnerService = $partnerService;
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
                $this->logger("Request", $content);
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

        $this->logger("Response", $response->getStatusCode()." ".$response->getBody());
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
     * 
     * @param string $info
     * @param string $message
     */
    private function logger($info, $message) {
        try {
            $writer = new \Zend\Log\Writer\Stream("/tmp/partner_signup.log");
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($this->partnerService->getRemoteAddress() ." " . $info . "\n" . $message);
        } catch (Exception $ex) {

        }
    }
}
