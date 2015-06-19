<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

// REMOVE DEVELOPMENTENVIORNMENT
class ReservationsController extends AbstractActionController
{

    // when this becomes more serious maybe it will be better move it in some better place, like an API module
    public function listAction()
    {
        $json = '{"status":200,"reason":"","data":[],"time":1434641823}';

        return new JsonModel(json_decode($json, true));
    }
}
