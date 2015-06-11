<?php

namespace Cartasi\Service;

use Cartasi\Entity\Contracts;
use Cartasi\Entity\Transactions;

class CartasiPaymentsService
{

	public function __construct()
	{

	}

	/**
	 * creates a Cartasi\Entity\Contracts entity with alias
	 * @param string
	 */
	public function createContract($alias)
	{
		$contract = new Contracts();
	}

	/**
	 * creates a Cartasi\Entity\Transactions entity with all necessary parameters
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function createTransaction($importo, $divisa, $email, $num_contratto)
	{
		$transaction = new Transactions();
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
			$string .= $param . '=' . $this->firstPaymentarams[$param];
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
}