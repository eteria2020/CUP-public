<?php

namespace Application\Controller;

// External Modules
use Zend\Mvc\Controller\AbstractActionController;
use SharengoCore\Service\PartnerService;
use SharengoCore\Service\TelepassPayService;
use SharengoCore\Service\TripPaymentsService;

class PartnerController extends AbstractActionController {

    /**
     *
     * @var PartnerService partnerService
     */
    private $partnerService;

    private $telepassPayService;
    private $tripPaymentsService;

    public function __construct(
        PartnerService $partnerService, 
        TelepassPayService $telepassPayService,
        TripPaymentsService $tripPaymentsService
    ) {
        $this->partnerService = $partnerService;
        $this->telepassPayService = $telepassPayService;
        $this->tripPaymentsService = $tripPaymentsService;
    }

    /**
     * API for partner
     * https://en.wikipedia.org/wiki/UTM_parameters
     * @param utm_source
     * @return json
     */
    public function getInfoAction() {

        if (isset($_GET["utm_source"])) {
            if (strtoupper($_GET["utm_source"]) == 'FREE2MOVE') {

                $param = "2MOVE";
                $response_msg = $this->partnerData($param);
                $values = array_values(array_map('intval', explode(",", $response_msg)));

                $response = $this->getResponse();
                $response->setStatusCode(200);
                $response->setContent(json_encode(array_combine(array("lead", "free2move"), $values)));
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
        $statusCode = 404;
        $partner = null;
        $partnerResponse = null;

        $response = $this->getResponse();
        $response->setStatusCode($statusCode);

        try {
            $this->testPayment();
            return $response;

            if ($this->getRequest()->isPost()) {
                //$authorization = $this->getRequest()->getHeader('Authorization', '');
 
                $content = file_get_contents('php://input');
                $contentArray = json_decode($content, true);

                //$this->userEventsService->saveNewEvent($webUser,  "customer-partner", $contentArray);     //TODO: to implement
                //$contentObject = json_decode($content);
                //$debug=$contentArray['partnerName'];
                $partnerCode = $this->partnerService->getPartnerCode($contentArray, 'partnerName');
                $partner = $this->partnerService->findEnabledBycode($partnerCode);

                if(!is_null($partner)) {
                    $statusCode = $this->partnerService->signup($partner, $contentArray, $partnerResponse);
                }
                //if ($authorization == 'telepassAPIKey') {
//                    if ($partnerName == 'telepass') {
//                        $statusCode = $this->telepassSignupMain($contentArray, $partnerResponse);
//                    }
                //}
            }

            if (!is_null($partnerResponse)) {
                $response->setContent(json_encode($partnerResponse));
            }
        } catch (\Exception $ex) {
            $response->setStatusCode(500);
        }

        return $response;
    }


    private function testPayment() {
        $tripPayments = $this->tripPaymentsService->getTripPaymentsForPayment(null, '-180 days', null, 200);
        //var_dump(count($tripPayments));
        //$response = $this->telepassPayService->sendTripPaymentRequest($tripPayments[0]);
        $customer = $tripPayments[0]->getCustomer();
        //var_dump($customer->getId());
        $response = $this->telepassPayService->sendPaymentRequest($customer, 456);
        var_dump($response);
    }
}
