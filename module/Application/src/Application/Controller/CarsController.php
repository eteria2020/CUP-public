<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Http\Client;

class CarsController extends AbstractRestfulController
{

    /**
     * @var string
     */
    private $url;

    public function __construct($url)
    {
        $this->url = sprintf($url, '');
    }

    public function getList()
    {
        $client = new Client($this->url, array(
            'maxredirects' => 0,
            'timeout'      => 30
        ));
        $response = $client->send();



        $json = '{"status":200,"reason":"","data":['.
            '{"plate":"DEMO1","model":"Test model","maker":"Test maker","lat":"45.468452","lon":"9.1857204","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"battery":20,"status":"operative"},'.
            '{"plate":"DEMO2","model":"Test model","maker":"Test maker","lat":"45.45","lon":"9.182","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"battery":30,"status":"operative"},'.
            '{"plate":"DEMO3","model":"Test model","maker":"Test maker","lat":"45.452","lon":"9.18","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true,"battery":50,"status":"operative"},'.
            '{"plate":"DEMO4","model":"Test model","maker":"Test maker","lat":"45.454","lon":"9.178","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"battery":22,"status":"operative"},'.
            '{"plate":"DEMO5","model":"Test model","maker":"Test maker","lat":"45.456","lon":"9.176","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"battery":100,"status":"operative"},'.
            '{"plate":"DEMO6","model":"Test model","maker":"Test maker","lat":"45.458","lon":"9.174","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true,"battery":34,"status":"operative"},'.
            '{"plate":"DEMO7","model":"Test model","maker":"Test maker","lat":"45.46","lon":"9.17","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true,"battery":54,"status":"operative"},'.
            '{"plate":"DEMO8","model":"Test model","maker":"Test maker","lat":"45.462","lon":"9.172","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":true,"battery":75,"status":"operative"},'.
            '{"plate":"DEMO9","model":"Test model","maker":"Test maker","lat":"45.464","lon":"9.19","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"battery":12,"status":"operative"},'.
            '{"plate":"DEMO10","model":"Test model","maker":"Test maker","lat":"45.466","lon":"9.188","internal_cleanliness":"","external_cleanliness":"","fuel_percentage":50,"busy":false,"battery":33,"status":"operative"}],"time":1434641823}';

        return new JsonModel(json_decode($response->getBody(), true));

        return new JsonModel(json_decode($json, true));
    }
 
    public function get($id)
    {
        // $id is plate
    }
}
