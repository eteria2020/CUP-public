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
use SharengoCore\Service\FleetService;
// Internal Modules
use Application\Service\RegistrationService;
use Multilanguage\Service\LanguageService;
use Application\Service\ProfilingPlaformService;
use Application\Exception\ProfilingPlatformException;
use Application\Form\RegistrationForm;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\PromoCodesOnceService;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\Fleet;
use SharengoCore\Service\TripsService;
use Zend\Log\Logger;
use SharengoCore\Service\EmailService as EmailService;

class PartnerController extends AbstractActionController {

    /**
     *
     * @var SharengoCore\Service\CustomersService
     */
    private $customersService;

    /**
     * @param CustomersService $customersService
     */
    public function __construct(
    CustomersService $customersService
    ) {

        $this->customersService = $customersService;
    }

    public function getInfoAction() {

        if (isset($_GET["name"])) {
            if (strtoupper($_GET["name"]) == 'FREE2MOVE') {
                $param = "2MOVE";

                $response_msg = $this->partnerData($param);

                $values = array_values(explode(",", $response_msg));

                $response = $this->getResponse();
                $response->setStatusCode(200);
                $response->setContent(json_encode(array_combine(array("lead", "free2move"), $values)));
                return $response;
            } else {
                $response = $this->getResponse();
                $response->setStatusCode(200);
                $response->setContent(json_encode(array("response" => "Parameters NOT VALID")));
                return $response;
            }
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array("response" => "Parameters NOT FOUND")));
            return $response;
        }
    }

    private function partnerData($param) {
        return $this->customersService->partnerData($param);
    }

}
