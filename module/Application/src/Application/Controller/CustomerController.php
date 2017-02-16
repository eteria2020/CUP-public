<?php

namespace Application\Controller;

use SharengoCore\Entity\Customers;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class CustomerController extends AbstractActionController
{
    public function customerDataAction()
    {
        $customer = $this->identity();

        if (!$customer instanceof Customers) {
            return $this->response->setStatusCode(403);
        }

        return new JsonModel([
            'name' => $customer->getName(),
            'surname' => $customer->getSurname(),
            'email' => $customer->getEmail()
        ]);
    }
}
