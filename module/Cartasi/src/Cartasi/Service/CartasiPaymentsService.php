<?php

namespace Cartasi\Service;

use Cartasi\Entity\Contracts;
use Cartasi\Entity\Transactions;

class PaymentsService
{

	/**
	 * @var mixed[]
	 */
	private $params;

	/**
	 * @param mixed[]
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * creates a Cartasi\Entity\Contracts entity with alias
	 */
	public function createContract()
	{
		if($this->params != null)
		{
			$alias = $this->params['alias'];
			$contract = new Contracts();
		}
	}

	/**
	 * creates a Cartasi\Entity\Transactions entity with all necessary parameters
	 */
	public function createTransaction()
	{
		if($this->params != null)
		{
			$importo = $this->params['importo'];
			$divisa = $this->params['divisa'];
			$mail = $this->params['email'];
			$num_contratto = $this->getContractNumber();
			$transaction = new Transactions();
		}
	}

	/**
	 * @return string
	 */
	private function getContractNumber()
	{
		// TODO retrieve contrct number
		return '';
	}

	/**
	 * @return string
	 */
	public function computeFirstMac()
	{
		return $this->generateMac(['codTrans','divisa','importo']);
	}

	/**
	 * @return bool
	 */
	public function verifyFirstMac()
	{

	}

	/**
	 * @return string
	 */
	public function computeRecurringMac()
	{

	}

	/**
	 * @return bool
	 */
	public function verifyRecurringMac()
	{

	}

	/**
	 * generates a 40 characters long string in 4-bit hexadecimal format
	 * using the SHA1 algorithm
	 * @param string[] the names of the parameters needed for the mac
	 * @return string the resulting string
	 */
	private function generateMac($params)
	{
		$string = '';
		for($params as $param)
		{
			$string .= $param . '=' . $this->params[$param];
		}
		$string .= $this->getSecretKey()
		return = sha1($string);
	}

	/**
	 * @return string
	 */
	private function getSecretKey()
	{
		// TODO retrieve secret key
		return '';
	}

	/**
	 * @return string
	 */
	public function getSessionId()
	{
		// TODO check if session is started
		return session_id();
	}

	/**
	 * @param string
	 */
	public function addGetParameters($url)
	{

	}
}