<?php

namespace Application\Controller;

// External Modules
use Zend\Mvc\Controller\AbstractActionController;
use SharengoCore\Service\PartnerService;

class PartnerController extends AbstractActionController {

    /**
     *
     * @var SharengoCore\Service\PartnerService
     */
    private $partnerService;

    /**
     * @param PartenerService $partnerService
     */
    public function __construct(
        PartnerService $partnerService
    ) {

        $this->partnerService = $partnerService;
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

}
