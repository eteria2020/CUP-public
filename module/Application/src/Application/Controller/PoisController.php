<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

// REMOVE DEVELOPMENTENVIORNMENT
class PoisController extends AbstractActionController
{

    // when this becomes more serious maybe it will be better move it in some better place, like an API module
    public function listAction()
    {
        $json = '{"status":200,"reason":"","data":['.
            '{"id":0,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.2","lat":"45.44","update":1},'.
            '{"id":1,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.205","lat":"45.445","update":1},'.
            '{"id":2,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.21","lat":"45.45","update":1},'.
            '{"id":3,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.215","lat":"45.455","update":1},'.
            '{"id":5,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.215","lat":"45.48","update":1},'.
            '{"id":4,"type":"test type","code":"","name":"","brand":"","address":"test address","town":"","zip_code":"","province":"","lon":"9.195","lat":"45.46","update":1}],"time":1434641823}';

        return new JsonModel(json_decode($json, true));
    }
}
