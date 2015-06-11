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

    /**
     * @param CartasiPaymentService
     */
    public function __construct(CartasiPaymentsService $cartasiService)
    {
        $this->cartasiService = $cartasiService;
    }

    public function firstPaymentAction()
    {
        $url = '';
        $email = $this->getEmail();
        $alias = $this->params()->fromQuery('alias');
        $codTrans = $this->params()->fromQuery('codTrans');
        $divisa = $this->params()->fromQuery('divisa');
        $importo = $this->params()->fromQuery('importo');

        $this->cartasiService->createContract($alias);
        $this->cartasiService->createTransaction($importo,
                                            $divisa,
                                            $email,
                                            $this->getContractNumber());

        $mac = $this->cartasiService->computeMac(['codTrans','divisa','importo'],[$codTrans,$divisa,$importo]);
        $sessionId = $this->cartasiService->getSessionId();

        // TODO add beginning of url

        $url .= '&mac=' . $mac;
        $url .= '&session_id=' . $sessionId;

        $this->redirect()->toUrl($url);
    }

    public function returnFirstPaymentAction()
    {
        $codTrans = $this->params()->fromQuery('codTrans');
        $esito = $this->params()->fromQuery('esito');
        $importo = $this->params()->fromQuery('importo');
        $divisa = $this->params()->fromQuery('divisa');
        $data = $this->params()->fromQuery('data');
        $orario = $this->params()->fromQuery('orario');
        $codAut = $this->params()->fromQuery('codAut');

        $mac = $this->cartasiService->computeMac(['codTrans','esito','importo','divisa','data','orario','codAut'],
                                                [$codTrans,$esito,$importo,$divisa,$data,$orario,$codAut]);
        
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
