<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

// REMOVE DEVELOPMENTENVIORNMENT
class CarsController extends AbstractActionController
{

    // when this becomes more serious maybe it will be better move it in some better place, like an API module
    public function listAction()
    {
        $json = '{"status":200,"reason":"","data":['.
            '{"plate":"DEMO1","model":"Test model","maker":"Test maker","lat":"45.468452","lon":"9.1857204","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false},'.
            '{"plate":"DEMO2","model":"Test model","maker":"Test maker","lat":"45.45","lon":"9.182","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false},'.
            '{"plate":"DEMO3","model":"Test model","maker":"Test maker","lat":"45.452","lon":"9.18","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true},'.
            '{"plate":"DEMO4","model":"Test model","maker":"Test maker","lat":"45.454","lon":"9.178","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false},'.
            '{"plate":"DEMO5","model":"Test model","maker":"Test maker","lat":"45.456","lon":"9.176","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false},'.
            '{"plate":"DEMO6","model":"Test model","maker":"Test maker","lat":"45.458","lon":"9.174","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true},'.
            '{"plate":"DEMO7","model":"Test model","maker":"Test maker","lat":"45.46","lon":"9.17","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true},'.
            '{"plate":"DEMO8","model":"Test model","maker":"Test maker","lat":"45.462","lon":"9.172","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true},'.
            '{"plate":"DEMO9","model":"Test model","maker":"Test maker","lat":"45.464","lon":"9.19","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false},'.
            '{"plate":"DEMO10","model":"Test model","maker":"Test maker","lat":"45.466","lon":"9.188","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false}],"time":1434641823}';

        return new JsonModel(json_decode($json, true));
    }
}
