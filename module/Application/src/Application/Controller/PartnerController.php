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
     * @param utm_source
     * @return json
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

}
