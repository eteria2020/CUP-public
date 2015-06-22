<?php

namespace Cartasi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\Url;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

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

        if (!$customerId) {
            return $this->notFoundAction();
        }

        $customer = $this->customersService->findById($customerId);

        if (is_null($customer)) {
            return $this->notFoundAction();
        }

        $alias = $this->cartasiConfig['alias'];
        $currency = $this->cartasiConfig['currency'];
        $amount = $this->cartasiConfig['first_payment_amount'];
        $description = $this->cartasiConfig['first_payment_description'];

        $contract = $this->cartasiService->createContract($customer);
        $codTrans = $this->cartasiService->createTransaction(
            $contract,
            $amount,
            $currency,
            true
        );
        $macKey = $this->cartasiConfig['mac_key'];
        $mac = $this->cartasiService->computeMac([
            'codTrans' => $codTrans,
            'divisa' => $currency,
            'importo' => $amount
        ], $macKey);

        $url = $this->cartasiConfig['first_payment_url'];
        $url = $this->cartasiService->buildUrl($url, [
            'alias' => $alias,
            'importo' => $amount,
            'divisa' => $currency,
            'codTrans' => $codTrans,
            'url' => $this->url->__invoke('cartasi/ritorno-primo-pagamento', [], ['force_canonical' => true]),
            'url_back' => $this->url->__invoke('cartasi/rifiutato-primo-pagamento', [], ['force_canonical' => true]),
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
            $this->getEventManager()->trigger('cartasi.first_payment.invalid_mac', $this, [
                'codTrans' => $codTrans,
                'esito' => $outcome,
                'importo' => $amount,
                'divisa' => $currency,
                'data' => $date,
                'orario' => $time,
                'codAut' => $codAut,
                'receivedMac' => $receivedMac,
                'url' => $this->getRequest()->getUriString(),
                'ts' => date('Y-m-d H:i:s')
            ]);
        }

        $transaction = $this->cartasiService->getTransaction($codTrans);
        $contractId = $this->params()->fromQuery('num_contratto');

        // verify the transaction data
        if (!$transaction) {
            $this->getEventManager()->trigger('cartasi.first_payment.wrong_transaction', $this, [
                'transactionId' => $codTrans,
                'contract_id' => $contractId,
                'currency' => $currency,
                'amount' => $amount,
                'url' => $this->getRequest()->getUriString(),
                'ts' => date('Y-m-d H:i:s')
            ]);
            return $this->notFoundAction();
        }

        if (!$this->cartasiService->verifyTransaction($transaction, [
            'contract_id' => $contractId,
            'currency' => $currency,
            'amount' => $amount
        ])) {
            $this->getEventManager()->trigger('cartasi.first_payment.wrong_transaction_data', $this, [
                'transactionId' => $transaction ? $transaction->getId() : 0,
                'contract_id' => $contractId,
                'currency' => $currency,
                'amount' => $amount,
                'url' => $this->getRequest()->getUriString(),
                'ts' => date('Y-m-d H:i:s')
            ]);
        }

        $contract = $this->cartasiService->getContract($contractId);

        if (!$contract) {
            $this->getEventManager()->trigger('cartasi.first_payment.wrong_contract', $this, [
                'transactionId' => $transaction ? $transaction->getId() : 0,
                'contract_id' => $contractId,
                'currency' => $currency,
                'amount' => $amount,
                'url' => $this->getRequest()->getUriString(),
                'ts' => date('Y-m-d H:i:s')
            ]);
            $contract = $transaction->getContract();
        }

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
                    'email' => $this->params()->fromQuery('mail'),
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

            if ($outcome == 'OK') {
                $eventManager = $this->getEventManager();
                $eventManager->trigger('successfulPayment', $this, [
                    'customer' => $contract->getCustomer()
                ]);
            }
        } catch (\Exception $e) {
            $this->getEventManager()->trigger('cartasi.first_payment.update_error', $this, [
                'contractId' => $contract->getId(),
                'transactionId' => $rtansaction->getId(),
                'errorMessage' => $e->getMessage(),
                'url' => $this->getRequest()->getUriString(),
                'ts' => date('Y-m-d H:i:s')
            ]);
        }

        return new ViewModel([
            'outcome' => $outcome,
            'message' => $this->params()->fromQuery('messaggio')
        ]);
    }

    public function rejectedFirstPaymentAction()
    {
        $codTrans = $this->params()->fromQuery('codTrans');
        $amount = $this->params()->fromQuery('importo');
        $currency = $this->params()->fromQuery('divisa');
        $outcome = $this->params()->fromQuery('esito');

        $transaction = $this->cartasiService->getTransaction($codTrans);

        try {
            $this->cartasiService->updateTransaction($transaction, [
                'outcome' => $outcome
            ]);
        } catch (\Exception $e) {
            $this->getEventManager()->trigger('cartasi.first_payment.return_update_error', $this, [
                'transactionId' => $transaction->getId(),
                'outcome' => $outcome,
                'errorMessage' => $e->getMessage(),
                'url' => $this->getRequest()->getUriString(),
                'ts' => date('Y-m-d H:i:s')
            ]);
        }

        return new ViewModel([
            'outcome' => $outcome
        ]);
    }

    public function recurringPaymentAction()
    {
        // get parameters from query string
        $amount = $this->params()->fromQuery('amount');
        $contractNumber = $this->params()->fromQuery('contract');

        if (!$contractNumber) {
            $this->getEventManager()->trigger('cartasi.recurring_payment.invalid_contract_number', $this, [
                'contractNumber' => $contractNumber,
                'amount' => $amount,
                'url' => $this->getRequest()->getUriString(),
                'ts' => date('Y-m-d H:i:s')
            ]);
            return $this->returnJsonWithOutcome('KO');
        }

        $contract = $this->cartasiService->getContract($contractNumber);

        if (!$contract) {
            $this->getEventManager()->trigger('cartasi.recurring_payment.invalid_contract', $this, [
                'contract' => $contract,
                'amount' => $amount,
                'url' => $this->getRequest()->getUriString(),
                'ts' => date('Y-m-d H:i:s')
            ]);
            return $this->returnJsonWithOutcome('KO');
        }

        if ($contract->isExpired()) {
            $this->getResponse()->setStatusCode(403);
            $this->getResponse()->setReasonPhrase('Contract is expired');
            return;
        }

        $email = $contract->getContactEmail();

        // get configuration values
        $alias = $this->cartasiConfig['alias'];
        $currency = $this->cartasiConfig['currency'];
        $description = $this->cartasiConfig['recurring_payment_description'];

        $codTrans = $this->cartasiService->createTransaction(
            $contract,
            $amount,
            $currency,
            false
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
            'scadenza' => $contract->getPanExpiry(),
            'mac' => $mac,
            'num_contratto' => $contract->getId(),
            'tipo_servizio' => 'paga_rico',
            'tipo_richiesta' => 'PR',
            'descrizione' => $description
        ]);

        $xml = $this->cartasiService->sendRecurringPaymentRequest($url);

        if (!$this->cartasiService->verifyResponse($xml, $macKey)) {
            $this->getEventManager()->trigger('cartasi.recurring_payment.wrong_data', $this, [
                'xml' => $xml->asXml(),
                'url' => $this->getRequest()->getUriString(),
                'ts' => date('Y-m-d H:i:s')
            ]);
        }

        try {
            $this->cartasiService->updateTransactionFromXml($xml);
        } catch (\Exception $e) {
            $this->getEventManager()->trigger('cartasi.recurring_payment.update_error', $this, [
                'xml' => $xml->asXml(),
                'url' => $this->getRequest()->getUriString(),
                'ts' => date('Y-m-d H:i:s')
            ]);
        }

        $outcome = $xml->StoreResponse->codiceEsito == 0 ? 'OK' : 'KO';
        return $this->returnJsonWithOutcome($outcome);
    }

    private function returnJsonWithOutcome($outcome)
    {
        return new JsonModel([
            'outcome' => $outcome
        ]);
    }
}
