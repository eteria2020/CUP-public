<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class ReservationsController extends AbstractRestfulController
{

    public function getList()
    {
        $json = '{"status":200,"reason":"","data":[],"time":1434641823}';

        return new JsonModel(json_decode($json, true));
    }
 
    public function create($data)
    {
        
    }
 
    public function delete($id)
    {
        
    }
}
