<?php

namespace Cartasi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Cartasi\Service\CartasiPaymentsService

class PaymentsController extends AbstractActionController
{

    public function firstPaymentAction()
    {
        // https://ecommerce.keyclient.it/ecomm/ecomm/DispatcherServlet?alias=valore&importo=5012&divisa=EUR &codTrans=990101- 00001&mail=xxx@xxxx.it&url=http://www.xxxxx.it&session_id=xxxxxxxx&mac=yyyy&languageId=ENG
        $url = '';
        $email = $this->params()->fromQuery('email');

        if (empty($email)) {
            throw \Exception('email non valida');
        }

        $cartasiService = new CartasiPaymentsService($this->params()->fromQuery());

        $cartasiService->createContract();
        $cartasiService->createTransaction();

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
        // https://ecommerce.keyclient.it/ecomm/ecomm/ServletS2S?alias=payment_test- soft&importo=1245&divisa=EUR&codTrans=ID000000000025483A&mail=prova@prova.it&url=http://www. test-shoponline.aa/esito_url&urlpost=http://www.test- shoponline.aa/esito_urlpost&parametro1=valore1&pan=525599******9992&scadenza=201506&cv2=123 &tipo_richiesta=PA&mac=f1ada78358acaaea85b0bb029bd74bec963c5452
        $url = '';

        $email = $this->params()->fromQuery('email');

        if (empty($email)) {
            throw \Exception('email non valida');
        }

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
}
