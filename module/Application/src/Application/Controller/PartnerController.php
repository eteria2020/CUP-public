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
     * @param Form $form1
     * @param Form $form2
     * @param RegistrationService $registrationService
     * @param CustomersService $customersService
     * @param LanguageService $languageService
     * @param ProfilingPlaformService $profilingPlatformService
     * @param Translator $translator
     * @param HydratorInterface $hydrator
     * @param TripsService $tripsService
     * @param PromoCodesService $promoCodeService
     * @param PromoCodesOnceService $promoCodesOnceService
     */
    public function __construct(
    CustomersService $customersService
    /* Form $form1, Form $form2, RegistrationService $registrationService,  LanguageService $languageService, ProfilingPlaformService $profilingPlatformService, Translator $translator, HydratorInterface $hydrator, array $smsConfig, EmailService $emailService, FleetService $fleetService, TripsService $tripsService
      , PromoCodesService $promoCodeService, PromoCodesOnceService $promoCodesOnceService */) {

        $this->customersService = $customersService;
        /*
          $this->form1 = $form1;
          $this->form2 = $form2;
          $this->registrationService = $registrationService;

          $this->languageService = $languageService;
          $this->profilingPlatformService = $profilingPlatformService;
          $this->translator = $translator;
          $this->hydrator = $hydrator;
          $this->smsConfig = $smsConfig;
          $this->emailService = $emailService;
          $this->fleetService = $fleetService;
          $this->tripsService = $tripsService;
          $this->promoCodeService = $promoCodeService;
          $this->promoCodesOnceService = $promoCodesOnceService; */
    }

    public function getInfoAction() {

        if (isset($_GET["name"])) {
            $response_msg = $this->partnerData(strtoupper($_GET["name"]));

            $values = array_values(explode(",", $response_msg));

            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array_combine(array("lead", "free2move"), $values)));
            return $response;
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array("response" => "Parameters NOT FOUND")));
            return $response;
        }
    }

    private function partnerData($name) {
        $response = $this->customersService->partnerData($name);
        return $response;
    }

}
