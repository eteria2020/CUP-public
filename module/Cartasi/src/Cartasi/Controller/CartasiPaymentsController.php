<?php

namespace Cartasi\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Cartasi\Service\CartasiPaymentsService;

class CartasiPaymentsController extends AbstractActionController
{

    /**
     * @var CartasiPaymentService
     */
    private $cartasiService;

    /**
     * @var array
     */
    private $cartasiConfig;

    /**
     * @param CartasiPaymentService
     */
    public function __construct(
        CartasiPaymentsService $cartasiService,
        array $cartasiConfig
    ) {
        $this->cartasiService = $cartasiService;
        $this->cartasiConfig = $cartasiConfig;
    }

    public function firstPaymentAction()
    {
        $email = $this->getEmailFromQuery();
        $alias = $this->cartasiConfig['alias'];
        $currency = $this->cartasiConfig['divisa'];
        $amount = $this->cartasiConfig['first_payment_amount'];

        $contractId = $this->cartasiService->createContract($email);
        $codTrans = $this->cartasiService->createTransaction(
            $contractId,
            $amount,
            $currency,
            $email
        );

        $macKey = $this->cartasiConfig['mac_key'];
        $mac = $this->cartasiService->computeMac([
            'codTrans' => $codTrans,
            'divisa' => $currency,
            'importo' => $amount
        ], $macKey);

        //$sessionId

        $url = $this->cartasiConfig['first_payment_url'];
        $url = $this->cartasiService->buildUrl($url, [
            'alias' => $alias,
            'importo' => $amount,
            'divisa' => $currency,
            'codTrans' => $codTrans,
            'url' => '', //TODO
            'url_back' => '', //TODO
            'mac' => $mac,
            'mail' => $email,
            'num_contratto' => $contractId,
            'tipo_servizio' => 'paga_rico',
        ]);

        $this->redirect()->toUrl($url);
    }

    public function returnFirstPaymentAction()
    {
        $codTrans = $this->params()->fromQuery('codTrans');
        $outcome = $this->params()->fromQuery('esito');
        $amount = $this->params()->fromQuery('importo');
        $currency = $this->params()->fromQuery('divisa');
        $date = $this->params()->fromQuery('data');
        $time = $this->params()->fromQuery('orario');
        $codAut = $this->params()->fromQuery('codAut');

        $macKey = $this->cartasiConfig['mac_key'];
        $computedMac = $this->cartasiService->computeMac([
            'codTrans' => $codTrans,
            'esito' => $outcome,
            'importo' => $amount,
            'divisa' => $currency,
            'data' => $date,
            'orario' => $time,
            'codAut' => $codAut
        ], $macKey);

        // check if the mac is correct
        $receivedMac = $this->params()->fromQuery('mac');
        if (!$this->cartasiService->verifyMac($computedMac, $receivedMac)) {
            throw new \Exception('InvalidMac');
        }

        $transaction = $this->cartasiService->getTransaction($codTrans);
        $contractId = $this->params()->fromQuery('num_contratto');

        // verify the transaction data
        if (!$this->cartasiService->verifyTransaction($transaction, [
            'contract_id' => $contractId,
            'currency' => $currency,
            'amount' => $amount
        ])) {
            throw new \Exception('InvalidTransactionData');
        }

        try {
            $this->cartasiService->updateContract($contractId, [
                'pan' => $this->params()->fromQuery('pan'),
                'pan_expiry' => $this->params()->fromQuery('scadenza_pan')
            ]);
            $this->cartasiService->updateTransaction($transaction, [
                'name' => $this->params()->fromQuery('nome'),
                'surname' => $this->params()->fromQuery('cognome'),
                'brand' => $this->params()->fromQuery('brand'),
                'outcome' => $outcome,
                'datetime' => $this->cartasiService->datetime($date, $time),
                'codAut' => $codAut,
                'region' => $this->params()->fromQuery('regione'),
                'country' => $this->params()->fromQuery('nazionalita'),
                'message' => $this->params()->fromQuery('messaggio'),
                'hash' => $this->params()->fromQuery('hash'),
                'check' => $this->params()->fromQuery('check'),
                'conventionCode' => $this->params()->fromQuery('codiceConvenzione'),
                'transactionType' => $this->params()->fromQuery('tipoTransazione'),
                'productType' => $this->params()->fromQuery('check')
            ]);
        } catch (\Exception $e) {
            //TODO
        }

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

        $email = $this->getEmailFromQuery();

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

    /**
     * retrieces user email from query string
     *
     * @return string
     * @throws \Exception email non valida
     */
    private function getEmailFromQuery()
    {
        $email = $this->params()->fromQuery('email', '');
        if (empty($email)) {
            throw new \Exception('EmailNotDefined');
        }
        return $email;
    }
}
