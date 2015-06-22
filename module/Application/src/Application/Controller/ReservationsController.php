<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class ReservationsController extends AbstractRestfulController
{

    /**
     * @var string
     */
    private $url;

    public function __construct($url)
    {
        $this->url = 'http://api.sharengo.it:8021/v2/reservations';//sprintf($url, '');

    }

    public function getList()
    {

    	$client = new Client($this->url, array(
            'maxredirects' => 0,
            'timeout'      => 30
        ));
        $response = $client->send();
        
        $json = '{"status":200,"reason":"","data":[],"time":1434641823}';

        return $response->getBody();
        return new JsonModel(json_decode($json, true));
    }
 
    public function create($data)
    {
        
    }
 
    public function delete($id)
    {
        
    }
}
