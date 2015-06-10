<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class PaymentsController extends AbstractActionController
{
    public function firstPaymentAction()
    {
        $url = '';
        $email = $this->params()->fromQuery('email');

        if (empty($email)) {
            throw \Exception('email non valida');
        }

        createContract()
        createTransaction()
        computeMac()
        getSessionId()
        addGetParameters($url)

        $this->redirect()->toUrl($url);
    }

    public function returnFirstPaymentAction()
    {
        getParameters()
        computeMac()
        verifyMac()
        getTransaction()
        checkTransactionData()
        updateContract()
        updateTransaction()

        return new ViewModel();
    }

    public function rejectedFirstPaymentAction()
    {
        getParameters()
        getTransaction()
        updateTransaction()

        return new ViewModel();
    }

    public function recurringPayment()
    {
        $url = '';

        $email = $this->params()->fromQuery('email');

        if (empty($email)) {
            throw \Exception('email non valida');
        }

        $this->redirect()->toUrl($url);
    }

    public function returnRecurringPayment()
    {

    }
}
