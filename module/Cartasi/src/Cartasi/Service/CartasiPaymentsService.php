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
	 * generates a 40 characters long string in 4-bit hexadecimal format
	 * using the SHA1 algorithm. Note that the order in which the parameters
	 * are set in the arrays affect the result
	 * @param string[] the names of the parameters needed for the mac
	 * @param string[] the values of the parameters needed for the mac
	 * @return string
	 */
	public function computeMac($paramNames, $params)
	{
		if(count($paramNames) != count($params))
		{
			return '';
		}

		$string = '';
		for($int i=0 ; i<count($paramNames) ; i++)
		{
			$string .= $paramNames[i] . '=' . $params[i];
		}
		$string .= $this->getSecretKey();

		return sha1($string);
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