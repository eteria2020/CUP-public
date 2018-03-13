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

    public function signupAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $debug ="";

        try {

            if ($this->getRequest()->isPost()) {
                $content = file_get_contents('php://input');
                //$contentObject = json_decode($content);
                $contentArray = json_decode($content,true);
                //$debug=$contentArray['partnerName'];
                $partnerName = $this->getDataFormatedLower($contentArray, "partnerName");
                if ($partnerName=="telepass"){
                    if($this->checkPartnerContentForTelepass($contentArray, $partnerResponse)) {
                        $this->partnerService->saveNewCustomer($contentArray);
    //                    $partnerResponse = array(
    //                        "created"=> false,
    //                        "userId"=>"",
    //                        "password"=>"",
    //                        "pin"=>"",
    //                        "debug" => $debug,
    //                        );



                    } else {
                        $response->setStatusCode(404);
                    }

                    $response->setStatusCode(200);
                    $response->setContent(json_encode($partnerResponse));
                }

                //$contentObject = json_decode($content);
                //$debug=$contentObject->{'username'};

                //$debug = $content;
                //$debug = var_dump($params);



            } else {
                $response->setStatusCode(404);
            }
        } catch (\Exception $ex) {
            $response->setStatusCode(500);
        }

        return $response;
    }

    /**
     * Check the Json data match with the constarints
     * @param string[] $contentArray
     * @param string[] $response
     * @return boolean
     */
    private function checkPartnerContentForTelepass($contentArray, &$response) {
        $debug = "";
        $strError ="";

        try {

            //if($contentArray["partnerName"]=="telepass") {
            //if($contentArray->{'username'}=="telepass") {
            $key = 'partnerName';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if($value=='telepass'){
                $contentArray[$key] = $value;
            } else {
                $strError .= ' Invalid value '. $key;
            }

            $key = 'gender';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if($value=='m') {
                $value = 'male';
            }
            if($value=='f') {
                $value = 'female';
            }
            if($value=='male' || $value=='female'){
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'name';
            $value = $this->getDataFormatedLower($contentArray, $key, FALSE);
            if(strlen($value)>=3){
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'surname';
            $value = $this->getDataFormatedLower($contentArray, $key, FALSE);
            if(strlen($value)>=3){
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'birthDate';
            $value = $this->getDataFormatedDateTime($contentArray, $key);
            if(!is_null($value)){

            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'birthTown';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if(strlen($value)>0){
                $contentArray[$key] = strtoupper($value);
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'birthProvince';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if(strlen($value)==2){
                $contentArray[$key] = strtoupper($value);
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'birthCountry';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if(strlen($value)==2){
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            
            $key = 'fiscalCode';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if(strlen($value)>0){
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

            $key = 'mobile';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if(strlen($value)>0){
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'email';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if(strlen($value)>0){
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'password';
            $value = $this->getDataFormatedLower($contentArray, $key, FALSE);
            if(strlen($value)>0){
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'pin';
            $value = $this->getDataFormatedLower($contentArray, $key);
            if(strlen($value)==4){
                $contentArray[$key] = $value;
            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'address';
            if( isset($contentArray[$key]) ){
                $address = $contentArray[$key];
                $key2 = 'street';
                $value = $this->getDataFormatedLower($address, $key2, FALSE);
                if(strlen($value)>0){
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

//                $key2 = 'streetNumber';
//                $value = $this->getDataFormatedLower($address, $key2, FALSE);
//                if(strlen($value)>0){
//                    $contentArray['address']['street'] = sprintf('%s, %s' , $contentArray['address']['street'], $value);
//                }

                $key2 = 'town';
                $value = $this->getDataFormatedLower($address, $key2, FALSE);
                if(strlen($value)>0){
                    $contentArray['address'][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'zip';
                $value = $this->getDataFormatedLower($address, $key2, FALSE);
                if(strlen($value)>0){
                    $contentArray['address'][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'province';
                $value = $this->getDataFormatedLower($address, $key2);
                if(strlen($value)==2){
                    $contentArray['address'][$key2] = strtoupper($value);
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'country';
                $value = $this->getDataFormatedLower($address, $key2);
                if(strlen($value)==2){
                    $contentArray['address'][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            $key = 'drivingLicense';
            if( isset($contentArray[$key]) ){
                $drivingLicense = $contentArray[$key];

                $key2 = 'number';
                $value = $this->getDataFormatedLower($drivingLicense, $key2);
                if(strlen($value)>0){
                    $contentArray[$key][$key2] = strtoupper($value);
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'country';
                $value = $this->getDataFormatedLower($drivingLicense, $key2);
                if(strlen($value)==2){
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'town';
                $value = $this->getDataFormatedLower($drivingLicense, $key2, FALSE);
                if(strlen($value)>0){
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'authority';
                $value = $this->getDataFormatedLower($drivingLicense, $key2);
                if($value=='dtt' || $value=='mc' || $value=='co' || $value=='ae' || $value=='uco' || $value=='pre'){
                    $contentArray[$key][$key2] = strtoupper($value);
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'realeaseDate';
                $value = $this->getDataFormatedDateTime($drivingLicense, $key2);
                if(!is_null($value)){
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'expire';
                $value = $this->getDataFormatedDateTime($drivingLicense, $key2);
                if(!is_null($value)){
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'firstname';
                $value = $this->getDataFormatedLower($drivingLicense, $key2, FALSE);
                if(strlen($value)>0){
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'surname';
                $value = $this->getDataFormatedLower($drivingLicense, $key2, FALSE);
                if(strlen($value)>0){
                    $contentArray[$key][$key2] = $value;
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'category';
                $value = $this->getDataFormatedLower($drivingLicense, $key2, FALSE);
                if(strlen($value)>0){
                    $contentArray[$key][$key2] = strtoupper($value);
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

                $key2 = 'foreign';
                $value = $this->getDataFormatedLower($drivingLicense, $key2);
                if($value=='true' || $value=='false'){
                    $contentArray[$key][$key2] = strtoupper($value);
                } else {
                    $strError .= sprintf('Invalid value [%s]. ', $key2);
                }

            } else {
                $strError .= sprintf('Invalid value [%s]. ', $key);
            }

            if($strError=="") {
                $result= true;
                $response = null;
            } else {
                $result= false;
                $response= array(
                    "uri"=> "partner/signup",
                    "status"=>401,
                    "statusFromProvider"=>false,
                    "message" => $strError,
                    "debug" => $debug,
                    );

            }

        } catch (\Exception $ex) {
            $result= false;
            $response= array(
                "uri"=> "partner/signup",
                "status"=>401,
                "statusFromProvider"=>false,
                "message" => $ex->getMessage(),
                );
        }

        return $result;

    }


    private function getDataFormatedLower($contentArray, $keyValue, $toLower = true) {
        $result = "";

        if(isset($contentArray[$keyValue])){
            if($toLower) {
                $result = trim(strtolower($contentArray[$keyValue]));
            } else {
                $result = trim($contentArray[$keyValue]);
            }
        }
        return $result;
    }

    private function getDataFormatedDateTime($contentArray, $keyValue) {
        $result = null;

        if(isset($contentArray[$keyValue])){
            if(is_array($contentArray[$keyValue])){
                if(count($contentArray[$keyValue])==3){
                    if(checkdate($contentArray[$keyValue][1],
                        $contentArray[$keyValue][2],
                        $contentArray[$keyValue][0])){
                        $result = date('Y-m-d', strtotime(sprintf('%d-%d-%d',$contentArray[$keyValue][0], $contentArray[$keyValue][1], $contentArray[$keyValue][2])));
                    }
                }
            }
        }
        return $result;
    }
}
