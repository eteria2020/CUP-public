<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Http\Client;

class PoisController extends AbstractRestfulController
{

    /**
     * @var string
     */
    private $url;

    public function __construct($url)
    {
        $this->url = 'http://api.sharengo.it:8021/v2/pois';//sprintf($url, '');

    }

    public function getList()
    {

        $client = new Client($this->url, array(
            'maxredirects' => 0,
            'timeout'      => 30
        ));
        $response = $client->send();
        
        $json = '{"status":200,"reason":"","data":['.
            '{"id":0,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.2","lat":"45.44","update":1},'.
            '{"id":1,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.205","lat":"45.445","update":1},'.
            '{"id":2,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.21","lat":"45.45","update":1},'.
            '{"id":3,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.215","lat":"45.455","update":1},'.
            '{"id":5,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.215","lat":"45.48","update":1},'.
            '{"id":4,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.195","lat":"45.46","update":1}],"time":1434641823}';

        return new JsonModel(json_decode($response->getBody(), true));
        return new JsonModel(json_decode($json, true));
    }
}
