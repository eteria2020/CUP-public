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
        $this->url = sprintf($url, '');
    }

    public function getList()
    {

        $client = new Client($this->url, array(
            'maxredirects' => 0,
            'timeout'      => 30
        ));

        $response = $client->send();
        
        return new JsonModel(json_decode($response->getBody(), true));
    }
}
