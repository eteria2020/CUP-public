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
	 * default constructor
	 * @param mixed[]
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * creates a Cartasi\Entity\Contracts entity with alias and num_contratto
	 */
	public function createContract()
	{
		if($this->params != null)
		{
			$num_contratto = $this->generateContractNumber();
			$contract = new Contracts();
		}
	}

	/**
	 * generates a unique string of max 30 characters
	 * may accept parameters in future implementation
	 * @return string
	 */
	private function generateContractNumber()
	{
		// TODO generate num_contratto
		return '';
	}

	public function createTransaction()
	{
		if($this->params != null)
		{
			$transaction = new Transactions();
		}
	}

	public function computeMac()
	{

	}

	public function getSessionId()
	{

	}

	public function addGetParameters()
	{

	}
}