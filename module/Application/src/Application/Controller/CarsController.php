<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class CarsController extends AbstractRestfulController
{

    public function getList()
    {
        $json = '{"status":200,"reason":"","data":['.
            '{"plate":"DEMO1","model":"Test model","maker":"Test maker","lat":"45.468452","lon":"9.1857204","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"km":20},'.
            '{"plate":"DEMO2","model":"Test model","maker":"Test maker","lat":"45.45","lon":"9.182","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"km":30},'.
            '{"plate":"DEMO3","model":"Test model","maker":"Test maker","lat":"45.452","lon":"9.18","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true,"km":50},'.
            '{"plate":"DEMO4","model":"Test model","maker":"Test maker","lat":"45.454","lon":"9.178","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"km":22},'.
            '{"plate":"DEMO5","model":"Test model","maker":"Test maker","lat":"45.456","lon":"9.176","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"km":100},'.
            '{"plate":"DEMO6","model":"Test model","maker":"Test maker","lat":"45.458","lon":"9.174","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true,"km":34},'.
            '{"plate":"DEMO7","model":"Test model","maker":"Test maker","lat":"45.46","lon":"9.17","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true,"km":54},'.
            '{"plate":"DEMO8","model":"Test model","maker":"Test maker","lat":"45.462","lon":"9.172","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true,"km":75},'.
            '{"plate":"DEMO9","model":"Test model","maker":"Test maker","lat":"45.464","lon":"9.19","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"km":12},'.
            '{"plate":"DEMO10","model":"Test model","maker":"Test maker","lat":"45.466","lon":"9.188","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"km":33}],"time":1434641823}';

        return new JsonModel(json_decode($json, true));
    }
 
    public function get($id)
    {
        // $id is plate
    }
}
