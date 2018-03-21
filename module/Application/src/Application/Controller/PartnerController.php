<?php

namespace Application\Controller;

// External Modules
use Zend\Mvc\Controller\AbstractActionController;
use SharengoCore\Service\PartnerService;
use SharengoCore\Service\UserEventsService;
use SharengoCore\Entity\Repository\ProvincesRepository;
use SharengoCore\Service\TelepassPayService;
use SharengoCore\Service\TripPaymentsService;

class PartnerController extends AbstractActionController {

    /**
     *
     * @var PartnerService partnerService
     */
    private $partnerService;

    /*
     * @var ProvincesRepository provincesRepository
     */
    private $provincesRepository;

    /*
     * @var UserEventsService userEventsService
     */
    private $userEventsService;

    private $telepassPayService;
    private $tripPaymentsService;

    public function __construct(
        PartnerService $partnerService, 
        ProvincesRepository $provincesRepository,
        UserEventsService $userEventsService,
        TelepassPayService $telepassPayService,
        TripPaymentsService $tripPaymentsService
    ) {
        $this->partnerService = $partnerService;
        $this->provincesRepository = $provincesRepository;
        $this->userEventsService = $userEventsService;
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
     *
     * TODO
     * - partners table (id, name, description, code, enable)
     * - userEvents, insert
     *
     * @return Request
     */
    public function signupAction() {
        $statusCode = 404;
        $partnerResponse = null;

        $response = $this->getResponse();

        try {
//            $telepassResponse = "";
//            $this->telepassPayService->sendPreAthorization(
//                    "test@email.com",
//                    "4234",
//                    array("reason"=>"ride"),
//                    432,
//                    "EUR",
//                    $telepassResponse);

//            $this->telepassPayService->tryPayment("432",
//                "54353",
//                42332,
//                "EUR",
//                $telepassResponse);
//
            $this->testPayment();
            return $response;

            if ($this->getRequest()->isPost()) {
                $authorization = $this->getRequest()->getHeader('Authorization', '');

                //var_dump($this->getRequest()->getHeader('Authorization',''));
                //var_dump($this->getRequest()->getHeaders());
                $content = file_get_contents('php://input');
                $contentArray = json_decode($content, true);

                //$this->userEventsService->saveNewEvent($webUser,  "customer-partner", $contentArray);     //TODO: to implement
                //$contentObject = json_decode($content);
                //$debug=$contentArray['partnerName'];
                $partnerName = $this->getDataFormatedLower($contentArray, 'partnerName');

                //if ($authorization == 'telepassAPIKey') {
                    if ($partnerName == 'telepass') {
                        $statusCode = $this->telepassSignupMain($contentArray, $partnerResponse);
                    }
                //}
            }

            $response->setStatusCode($statusCode);
            if (!is_null($partnerResponse)) {
                $response->setContent(json_encode($partnerResponse));
            }
        } catch (\Exception $ex) {
            $response->setStatusCode(500);
        }

        return $response;
    }

    private function telepassSignupMain($contentArray, &$partnerResponse) {
        $partnerResponse = null;
        $response = 200;
        $debug = "";

        if ($this->telepassSignupCheckAndFormat($contentArray, $partnerResponse)) {

            $customerOld = $this->partnerService->findCustomerByMainFields(
                    $contentArray['email'], $contentArray['fiscalCode'], $contentArray['drivingLicense']['number']);

//            var_dump($contentArray['birthDate']);
//            var_dump($contentArray['drivingLicense']['releaseDate']);
//            var_dump($this->provincesRepository->findOneBy(array('code' => 'RE')));
//            return;

            if (is_null($customerOld)) {
                $customerNew = $this->partnerService->saveNewCustomer($contentArray);
                if (!is_null($customerNew)) {
                    $partnerResponse = array(
                        "created" => true,
                        "userId" => $customerNew->getId(),
                        "password" => $customerNew->getPassword(),
                        "pin" => $customerNew->getPrimaryPin(),
                        "debug" => $debug,
                    );
                } else {
                    $partnerResponse = array(
                        "uri" => "partner/signup",
                        "status" => 401,
                        "statusFromProvider" => false,
                        "message" => "insert fail",
                        "debug" => $debug,
                    );
                }
            } else { // else, customer alredy exist
                $partnerResponse = array(
                    "created" => false,
                    "userId" => $customerOld->getId(),
                    "password" => $customerOld->getPassword(),
                    "pin" => $customerOld->getPrimaryPin(),
                    "debug" => $debug,
                );
            }
        } else {
            $response = 404;
        }

        return $response;
    }

    /**
     * Check the Json data match with the constarints
     * 
     * @param array $contentArray
     * @param array $response
     * @return boolean
     */
    private function telepassSignupCheckAndFormat(&$contentArray, &$response) {
        $debug = "";
        $strError = "";

        try {

            //if($contentArray["partnerName"]=="telepass") {
            //if($contentArray->{'username'}=="telepass") {
            $key = 'partnerName';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if ($value == 'telepass') {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'gender';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if ($value == 'm') {
                $value = 'male';
            }
            if ($value == 'f') {
                $value = 'female';
            }
            if ($value == 'male' || $value == 'female') {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'name';
            $value = $this->getDataFormatedLower($contentArray, $key, FALSE);
            if (strlen($value) >= 3) {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'surname';
            $value = $this->getDataFormatedLower($contentArray, $key, FALSE);
            if (strlen($value) >= 3) {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'birthDate';
            $value = $this->getDataFormatedDateTime($contentArray, $key);
            if (!is_null($value)) {
                
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'birthTown';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if (strlen($value) > 0) {
                $contentArray[$key] = strtoupper($value);
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'birthProvince';
            $value = $this->getDataFormatedLower($contentArray, $key);
            $province = $this->provincesRepository->findOneBy(array('code' => strtoupper($value)));
            if (!is_null($province)) {
                $contentArray[$key] = $province->getCode();
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'birthCountry';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if (strlen($value) == 2) {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }


            $key = 'fiscalCode';    //TODO: additional check
            $value = $this->getDataFormatedLower($contentArray, $key);
            if (strlen($value) > 0) {
                $contentArray[$key] = strtoupper($value);
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'vat';
            $value = $this->getDataFormatedLower($contentArray, $key);
            $contentArray[$key] = strtoupper($value);

            $key = 'phone';
            $value = $this->getDataFormatedLower($contentArray, $key);
            $contentArray[$key] = $value;

            $key = 'mobile'; //TODO: additional check
            $value = $this->getDataFormatedLower($contentArray, $key);
            if (strlen($value) > 0) {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'email'; //TODO: additional check
            $value = $this->getDataFormatedLower($contentArray, $key);
            if (strlen($value) > 0) {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'password';
            $value = $this->getDataFormatedLower($contentArray, $key, FALSE);
            if (strlen($value) > 0) {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'pin';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if (strlen($value) == 4 && is_numeric($value)) {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'address';
            if (isset($contentArray[$key])) {
                $address = $contentArray[$key];
                $key2 = 'street';
                $value = $this->getDataFormatedLower($address, $key2, FALSE);
                if (strlen($value) > 0) {
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

//                $key2 = 'streetNumber';
//                $value = $this->getDataFormatedLower($address, $key2, FALSE);
//                if(strlen($value)>0){
//                    $contentArray['address']['street'] = sprintf('%s, %s' , $contentArray['address']['street'], $value);
//                }

                $key2 = 'town';
                $value = $this->getDataFormatedLower($address, $key2, FALSE);
                if (strlen($value) > 0) {
                    $contentArray['address'][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'zip';
                $value = $this->getDataFormatedLower($address, $key2, FALSE);
                if (strlen($value) > 0) {
                    $contentArray['address'][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'province';
                $value = $this->getDataFormatedLower($address, $key2);
                $province = $this->provincesRepository->findOneBy(array('code' => strtoupper($value)));
                if (!is_null($province)) {
                    $contentArray['address'][$key2] = $province->getCode();
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'country';
                $value = $this->getDataFormatedLower($address, $key2);
                if (strlen($value) == 2) {
                    $contentArray['address'][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'drivingLicense';
            if (isset($contentArray[$key])) {
                $drivingLicense = $contentArray[$key];

                $key2 = 'number';
                $value = $this->getDataFormatedLower($drivingLicense, $key2);
                if (strlen($value) > 0) {
                    $contentArray[$key][$key2] = strtoupper($value);
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'country';
                $value = $this->getDataFormatedLower($drivingLicense, $key2);
                if (strlen($value) == 2) {
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'town';
                $value = $this->getDataFormatedLower($drivingLicense, $key2, FALSE);
                if (strlen($value) > 0) {
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'authority';
                $value = $this->getDataFormatedLower($drivingLicense, $key2);
                if ($value == 'dtt' || $value == 'mc' || $value == 'co' || $value == 'ae' || $value == 'uco' || $value == 'pre') {
                    $contentArray[$key][$key2] = strtoupper($value);
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'releaseDate';
                $value = $this->getDataFormatedDateTime($drivingLicense, $key2);
                if (!is_null($value)) {
                    
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'expire';
                $value = $this->getDataFormatedDateTime($drivingLicense, $key2);
                if (!is_null($value)) {
                    
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'firstname';
                $value = $this->getDataFormatedLower($drivingLicense, $key2, FALSE);
                if (strlen($value) > 0) {
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'surname';
                $value = $this->getDataFormatedLower($drivingLicense, $key2, FALSE);
                if (strlen($value) > 0) {
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'category';
                $value = $this->getDataFormatedLower($drivingLicense, $key2, FALSE);
                if (strlen($value) > 0) {
                    $contentArray[$key][$key2] = strtoupper($value);
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }

                $key2 = 'foreign';
                $value = $this->getDataFormatedLower($drivingLicense, $key2);
                if ($value == 'true' || $value == 'false') {
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s][%s]. ', $key, $key2);
                }
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'generalCondition1';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if ($value == 'true' || $value == 'false') {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'generalCondition2';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if ($value == 'true' || $value == 'false') {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'privacyCondition';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if ($value == 'true' || $value == 'false') {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'privacyInformation';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if ($value == 'true' || $value == 'false') {
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            if ($strError == '') {
                $result = true;
                $response = null;
            } else {
                $result = false;
                $response = array(
                    "uri" => "partner/signup",
                    "status" => 401,
                    "statusFromProvider" => false,
                    "message" => $strError,
                    "debug" => $debug,
                );
            }
        } catch (\Exception $ex) {
            $result = false;
            $response = array(
                "uri" => "partner/signup",
                "status" => 401,
                "statusFromProvider" => false,
                "message" => $ex->getMessage(),
            );
        }

        return $result;
    }

    private function getDataFormatedLower(array $contentArray, $keyValue, $toLower = true) {
        $result = "";

        if (isset($contentArray[$keyValue])) {
            if ($toLower) {
                $result = trim(strtolower($contentArray[$keyValue]));
            } else {
                $result = trim($contentArray[$keyValue]);
            }
        }
        return $result;
    }

    private function getDataFormatedDateTime(array $contentArray, $keyValue) {
        $result = null;

        if (isset($contentArray[$keyValue])) {
            if (is_array($contentArray[$keyValue])) {
                if (count($contentArray[$keyValue]) == 3) {
                    if (checkdate($contentArray[$keyValue][1], $contentArray[$keyValue][2], $contentArray[$keyValue][0])) {
                        $result = date('Y-m-d', strtotime(sprintf('%d-%d-%d', $contentArray[$keyValue][0], $contentArray[$keyValue][1], $contentArray[$keyValue][2])));
                    }
                }
            }
        }
        return $result;
    }

    private function testPayment() {
        $tripPayments = $this->tripPaymentsService->getTripPaymentsForPayment(null, '-180 days', null, 200);
        //var_dump(count($tripPayments));
        $response = $this->telepassPayService->sendPaymentRequest($tripPayments[0]);
        var_dump($response);
    }
}
