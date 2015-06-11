<?php

namespace Cartasi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Cartasi\Service\CartasiPaymentsService;

class CartasiPaymentsController extends AbstractActionController
{

    /**
     * @var CartasiPaymentService
     */
    private $cartasiService;

    public function __construct(CartasiPaymentsService $cartasiService)
    {
        $this->cartasiService = $cartasiService;
    }

    public function firstPaymentAction()
    {
        $url = '';
        $email = $this->getEmail();

        $cartasiService->createContract($this->params()->fromQuery('alias'));
        $cartasiService->createTransaction($this->params()->fromQuery('importo'),
                                            $this->params()->fromQuery('divisa'),
                                            $email,
                                            $this->getContractNumber());
        $mac = $cartasiService->computeFirstMac();
        $sessionId = $cartasiService->getSessionId();
        
        $url .= '&mac=' . $mac;
        $url .= '&session_id=' . $sessionId;

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

        $email = $this->getEmail();

        getContract()
        checkCardExiryDate()
        getNumContratto()
        createTransaction()
        computeMac()
        addParameters($url)

        $this->redirect()->toUrl($url);
    }

    public function returnRecurringPayment()
    {
        getParameters()
        getTransaction()
        updateTransaction()
    }

    private function getEmail()
    {
        $email = $this->params()->fromQuery('email');
        if (empty($email)) {
            throw \Exception('email non valida');
        }
        return $email;
    }



}
