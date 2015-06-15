<?php

namespace Cartasi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\Url;

use Cartasi\Service\CartasiPaymentsService;
use SharengoCore\Service\CustomersService;

class CartasiPaymentsController extends AbstractActionController
{

    /**
     * @var CartasiPaymentService
     */
    private $cartasiService;

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var array
     */
    private $cartasiConfig;

    /**
     * @var Zend\View\Helper\Url
     */
    private $url;

    /**
     * @param CartasiPaymentService
     */
    public function __construct(
        CartasiPaymentsService $cartasiService,
        CustomersService $customersService,
        array $cartasiConfig,
        Url $url
    ) {
        $this->cartasiService = $cartasiService;
        $this->customersService = $customersService;
        $this->cartasiConfig = $cartasiConfig;
        $this->url = $url;
    }

    public function firstPaymentAction()
    {
        $customerId = $this->params()->fromQuery('customer', '');
        if (empty($customerId)) {
            // TODO
        }
        $customer = $this->customersService->findById($customerId);

        $alias = $this->cartasiConfig['alias'];
        $currency = $this->cartasiConfig['currency'];
        $amount = $this->cartasiConfig['first_payment_amount'];
        $description = $this->cartasiConfig['first_payment_description'];

        $contract = $this->cartasiService->createContract($customer);
        $codTrans = $this->cartasiService->createTransaction(
            $contract,
            $amount,
            $currency
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
            'mail' => $customer->getEmail(),
            'num_contratto' => $contract->getId(),
            'tipo_servizio' => 'paga_rico',
            'descrizione' => $description
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

        // check if the mac is correct
        $receivedMac = $this->params()->fromQuery('mac');
        if (!$this->cartasiService->verifyMac($receivedMac, [
            'codTrans' => $codTrans,
            'esito' => $outcome,
            'importo' => $amount,
            'divisa' => $currency,
            'data' => $date,
            'orario' => $time,
            'codAut' => $codAut
        ], $macKey)) {
            // TODO
        }

        $transaction = $this->cartasiService->getTransaction($codTrans);
        $contractId = $this->params()->fromQuery('num_contratto');

        // verify the transaction data
        if (!$this->cartasiService->verifyTransaction($transaction, [
            'contract_id' => $contractId,
            'currency' => $currency,
            'amount' => $amount
        ])) {
            // TODO
        }

        $contract = $this->cartasiService->getContract($contractId);

        try {
            $this->cartasiService->updateTransactionAndContract(
                $contract,
                $transaction,
                [
                    'pan' => $this->params()->fromQuery('pan'),
                    'pan_expiry' => $this->params()->fromQuery('scadenza_pan')
                ],
                [
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
                ]
            );
        } catch (\Exception $e) {
            // TODO
        }

        return new ViewModel();
    }

    public function rejectedFirstPaymentAction()
    {
        $codTrans = $this->params()->fromQuery('codTrans');
        $amount = $this->params()->fromQuery('importo');
        $currency = $this->params()->fromQuery('divisa');
        $outcome = $this->params()->fromQuery('esito');

        $transaction = $this->cartasiService->getTransaction($codTrans);
        $this->cartasiService->updateTransaction($transaction, [
            'esito' => $outcome
        ]);

        return new ViewModel();
    }

    public function recurringPaymentAction()
    {
        // get parameters from query string
        $amount = $this->params()->fromQuery('amount');
        $contractNumber = $this->params()->fromQuery('contact');

        $contract = $this->cartasiService->getContract($contractNumber);

        if ($contract->isExpired()) {
            // TODO
        }

        $email = $contract->getContactEmail();

        // get configuration values
        $alias = $this->cartasiConfig['alias'];
        $currency = $this->cartasiConfig['currency'];
        $description = $this->cartasiConfig['recurring_payment_description'];

        $codTrans = $this->cartasiService->createTransaction(
            $contract->getId(),
            $amount,
            $currency,
            $contract->getCustomer()->getId()
        );

        $macKey = $this->cartasiConfig['mac_key'];
        $mac = $this->cartasiService->computeMac([
            'codTrans' => $codTrans,
            'divisa' => $currency,
            'importo' => $amount
        ], $macKey);

        $url = $this->cartasiConfig['recurring_payment_url'];
        $url = $this->cartasiService->buildUrl($url, [
            'alias' => $alias,
            'importo' => $amount,
            'divisa' => $currency,
            'codTrans' => $codTrans,
            'mail' => $email,
            'url' => '', //TODO
            'scadenza' => $contratto->getExpiryDate(),
            'mac' => $mac,
            'num_contratto' => $contract->getId,
            'tipo_servizio' => 'paga_rico',
            'tipo_richiesta' => 'PR',
            'descrizione' => $description
        ]);

        $this->redirect()->toUrl($url);
    }

    public function returnRecurringPaymentAction()
    {
        $xml = $this->params->fromQuery('xml');

        $response = $this->cartasiService->parseXml($xml);

        $macKey = $this->cartasiConfig['mac_key'];
        if (!$this->cartasiService->verifyRespose($response, $macKey)) {
            // TODO
        }

        $this->cartasiService->updateTransactionFormResponse($response);
    }
}
