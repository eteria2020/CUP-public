<?php

namespace Cartasi\Service;

use Cartasi\Entity\Contracts;
use Cartasi\Entity\Transactions;
use Cartasi\Entity\Repository\TransactionsRepository;
use Cartasi\Entity\Repository\ContractsRepostitory;

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

    public function __construct($transactionsRepository, $contractsRepository)
    {
        $this->transactionsRepository = $transactionsRepository;
        $this->contractsRepository = $contractsRepository;
    }

    /**
     * creates a Cartasi\Entity\Contracts for the given user and returns
     * the contract id
     *
     * @param int
     * @return int
     */
    public function createContract($customerId)
    {

    }

    /**
     * creates a Cartasi\Entity\Transactions entity with all necessary parameters
     * and returns the transaction id
     *
     * @param int
     * @param int
     * @param string
     * @param int
     * @return int
     */
    public function createTransaction($contractId, $amount, $currency, $customerId)
    {

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

        }

        $mac .= $macKey;

        return $mac;
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
        foreach ($params as $key => $param) {

        }

        return $url;
    }

    /**
     * retrieves a transaction from its id
     *
     * @param int
     * @return Cartasi\Entity\Transactions
     */
    public function getTransaction($transactionId)
    {
        return $this->transactionsRepository->findById($transactionId);
    }

    /**
     * retrieves a contract from its id
     *
     * @param inr
     * @return Cartasi\Entity\Contracts
     */
    public function getContract($contractId)
    {
        return $this->contractsRepository_>findById($contractId);
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

    }

    /**
     * updates the contract with the data received in the response
     *
     * @param int
     * @param array
     */
    public function updateContract($contractId, array $params)
    {

    }

    /**
     * updates the transaction with the data received in the response
     *
     * @param Cartasi\Entity\Transactions
     * @param array
     */
    public function updateTransaction(Transactions $transaction, array $params)
    {

    }

    /**
     * parses the xml response
     *
     * @param string
     * @return array
     */
    public function parseXml($xml)
    {

    }

    /**
     * verifies that the response is correct
     *
     * @param array
     * @return boolean
     */
    public function verifyResponse(array $response)
    {

    }

    /**
     * updates the transaction with the parameters received in the response
     *
     * @param array
     */
    public function updateTransactionFormResponse(array $response)
    {

    }
}
