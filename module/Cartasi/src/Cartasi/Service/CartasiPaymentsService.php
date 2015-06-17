<?php

namespace Cartasi\Service;

use Cartasi\Entity\Contracts;
use Cartasi\Entity\Transactions;
use Cartasi\Entity\Repository\TransactionsRepository;
use Cartasi\Entity\Repository\ContractsRepository;
use SharengoCore\Entity\Customers;

use Doctrine\ORM\EntityManager;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Http\Client;
use Zend\Http\Response;

class CartasiPaymentsService
{
    /**
     * @var TransactionsRepository
     */
    private $transactionsRepository;

    /**
     * @var ContractsRepository
     */
    private $contractsRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Zend\Filter\Word\UnderscoreToCamelCase
     */
    private $underscoreToCamelCase;

    /**
     * @var Zend\Http\Client
     */
    private $client;

    public function __construct(
        TransactionsRepository $transactionsRepository,
        ContractsRepository $contractsRepository,
        EntityManager $entityManager,
        UnderscoreToCamelCase $underscoreToCamelCase,
        Client $client
    ) {
        $this->transactionsRepository = $transactionsRepository;
        $this->contractsRepository = $contractsRepository;
        $this->entityManager = $entityManager;
        $this->underscoreToCamelCase = $underscoreToCamelCase;
        $this->client = $client;

        $this->client->setOptions([
            'sslverifypeer' => false
        ]);
    }

    /**
     * creates a Cartasi\Entity\Contracts for the given user and returns
     * the contract id
     *
     * @param SharengoCore\Entity\Customers
     * @return Contracts
     */
    public function createContract(Customers $customer)
    {
        $contract = new Contracts();
        $contract->setCustomer($customer);

        $this->entityManager->persist($contract);
        $this->entityManager->flush();

        return $contract;
    }

    /**
     * creates a Cartasi\Entity\Transactions entity with all necessary parameters
     * and returns the transaction id
     *
     * @param Contracts
     * @param int
     * @param string
     * @param boolean
     * @return int
     */
    public function createTransaction(Contracts $contract, $amount, $currency, $isFirstPayment)
    {
        $transaction = new Transactions();

        $transaction->setContract($contract);
        $transaction->setAmount($amount);
        $transaction->setCurrency($currency);
        $transaction->setIsFirstPayment($isFirstPayment);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $transaction->getId();
    }

    /**
     * generates a 40 characters long string in 4-bit hexadecimal format
     * using the SHA1 algorithm. Note that the order in which the parameters
     * are set in the arrays affects the result
     *
     * @param array
     * @param string
     * @return string
     */
    public function computeMac(array $params, $macKey)
    {
        $mac = '';
        foreach ($params as $key => $value) {
            $mac .= $key.'='.$value;
        }

        $mac .= $macKey;

        return sha1($mac);
    }

    /**
     * verifies if the received mac is correct with regards to the parameters
     * that were passed and to the mac key
     *
     * @param string
     * @param array
     * @param string
     * @return boolean
     */
    public function verifyMac($receivedMac, array $params, $macKey)
    {
        $mac = $this->computeMac($params, $macKey);

        return $receivedMac === $mac;
    }

    /**
     * build the url to be used for the payments from the base url and
     * the parameters that need to be passed
     *
     * @param string
     * @param array
     * @return string
     */
    public function buildUrl($url, $params)
    {
        $url .= '?';

        $arrayParams = array_map(function ($k, $v) {
            return $k.'='.$v;
        }, array_keys($params), $params);

        $url .= implode('&', $arrayParams);

        return $url;
    }

    /**
     * retrieves a transaction from its id
     *
     * @param int
     * @return Cartasi\Entity\Transactions|null
     */
    public function getTransaction($transactionId)
    {
        return $this->transactionsRepository->findOneById($transactionId);
    }

    /**
     * retrieves a contract from its id
     *
     * @param inr
     * @return Cartasi\Entity\Contracts
     */
    public function getContract($contractId)
    {
        return $this->contractsRepository->findOneById($contractId);
    }

    /**
     * retrieves the getter method for the given field
     *
     * @param string
     * @return string
     */
    private function getter($field)
    {
        return 'get'.ucfirst($this->underscoreToCamelCase->filter($field));
    }

    /**
     * retrieves the setter method for the given field
     */
    private function setter($field)
    {
        return 'set'.ucfirst($this->underscoreToCamelCase->filter($field));
    }

    /**
     * verifies the correctness of the transaction between the data in the database
     * and the data received in the response
     *
     * @param Cartasi\Entity\Transactions
     * @param array
     * @return boolean
     */
    public function verifyTransaction(Transactions $transaction, array $params)
    {
        foreach ($params as $key => $value) {
            $method = $this->getter($key);
            if ($value != $transaction->$method()) {
                return false;
            };
        }

        return true;
    }

    /**
     * updates the contract with the data received in the response
     *
     * @param Cartasi\Entity\Contracts
     * @param array
     */
    private function updateContract(Contracts $contract, array $params)
    {
        foreach ($params as $key => $value) {
            $method = $this->setter($key);
            $contract->$method($value);
        }

        $this->entityManager->persist($contract);
        $this->entityManager->flush();
    }

    /**
     * updates the transaction with the data received in the response
     *
     * @param Cartasi\Entity\Transactions
     * @param array
     */
    public function updateTransaction(Transactions $transaction, array $params)
    {
        foreach ($params as $key => $value) {
            $method = $this->setter($key);
            $transaction->$method($value);
        }

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }

    /**
     * updates the contract and the transaction, wrapping everything in a transaction
     *
     * @param Cartasi\Entity\Contracts
     * @param Cartasi\Entity\Transactions
     * @param array
     * @param array
     */
    public function updateTransactionAndContract(
        Contracts $contract,
        Transactions $transaction,
        array $contractParams,
        array $transactionParams
    ) {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->updateContract($contract, $contractParams);
            $this->updateTransaction($transaction, $transactionParams);

            // set the first payment as payed
            if ($transaction->getOutcome() == 'OK') {
                $this->setFirstPaymentCompleted($contract);
            }

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * set the first payment as completed
     *
     * @param Contracts
     */
    private function setFirstPaymentCompleted(Contracts $contract)
    {
        $customer = $contract->getCustomer();

        $customer->setFirstPaymentCompleted(true);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }

    /**
     * parses the xml response
     *
     * @param string
     * @return \SimpleXMLElement
     */
    public function parseXml($xml)
    {
        $response = new \SimpleXMLElement($xml);

        return $response;
    }

    /**
     * verifies that the response is correct
     *
     * @param \SimpleXMLElement
     * @param string
     * @return boolean
     */
    public function verifyResponse(\SimpleXMLElement $response, $macKey)
    {
        $storeRequest = $response->StoreRequest;
        $storeResponse = $response->StoreResponse;

        if (!$this->verifyMac($storeResponse->mac, [
            'codTrans' => $storeRequest->codTrans,
            'divisa' => $storeRequest->divisa,
            'importo' => $storeRequest->importo,
            'codAut' => $storeResponse->codiceAutorizzazione,
            'data' => explode("T", $storeResponse->dataOra)[0],
            'orario' => explode("T", $storeResponse->dataOra)[1]
        ], $macKey)) {
            return false;
        };

        $transaction = $this->getTransaction($storeRequest->codTrans);

        if (!$transaction) {
            return false;
        }

        $contractId = $storeRequest->num_contratto;
        $amount = $storeRequest->importo;
        $currency = $storeRequest->divisa;

        return $this->verifyTransaction($transaction, [
            'contract_id' => $contractId,
            'amount' => $amount,
            'currency' => $currency
        ]);
    }

    /**
     * updates the transaction with the parameters received in the response
     *
     * @param \SimpleXMLElement
     */
    public function updateTransactionFromXml(\SimpleXMLElement $response)
    {
        $storeRequest = $response->StoreRequest;
        $storeResponse = $response->StoreResponse;

        $transaction = $this->getTransaction($storeRequest->codTrans);

        $this->updateTransaction($transaction, [
            'brand' => $storeResponse->tipoCarta,
            'transactionType' => $storeResponse->transactionType,
            'email' => $storeRequest->mail,
            'region' => $storeResponse->regione,
            'country' => $storeResponse->paese,
            'productType' => $storeResponse->tipoProdotto,
            'check' => $storeResponse->check,
            'conventionCode' => $storeResponse->codiceConvenzione,
            'hash' => $storeResponse->hash,
            'codAut' => $storeResponse->codeAut,
            'dateTime' => date_create_from_format('YmdHis', str_replace("T", "", $storeResponse->dataOra)),
            'outcome' => $storeResponse->codiceEsito.' - '.
                $storeResponse->descrizioneEsito,
            'message' => $storeResponse->dettagliEsito
        ]);
    }

    /**
     * @var string format aaaammgg
     * @var string format hhmmss
     */
    public function datetime($date, $time)
    {
        return date_create_from_format('YmdHis', $date.$time);
    }

    /**
     * @var Customers
     * @return boolean
     */
    public function customerCompletedFirstPayment(Customers $customer)
    {
        $transaction = $this->transactionsRepository->findOneWithCompletedFirstPayment($customer);

        return !is_null($rtansaction);
    }

    /**
     * @var string
     * @return \SimpleXMLElement
     */
    public function sendRecurringPaymentRequest($url)
    {
        $request = new \Zend\Http\Request();
        $request->setUri($url);

        $response = $this->client->send($request);

        return simplexml_load_string($response->getBody());
    }
}
