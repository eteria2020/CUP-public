<?php

namespace Cartasi\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transactions
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="Cartasi\Entity\Repository\TransactionsRepository")
 */
class Transactions
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="transactions_id_seq", allocationSize=1, initialValue=20000)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer", nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=false)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @var \Contracts
     *
     * @ORM\ManyToOne(targetEntity="Contracts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contract_id", referencedColumnName="id")
     * })
     */
    private $contract;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", nullable=true)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", nullable=true)
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="outcome", type="text", nullable=true)
     */
    private $outcome;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=true)
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_aut", type="string", nullable=true)
     */
    private $codAut;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", nullable=true)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", nullable=true)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", nullable=true)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="`check`", type="string", nullable=true)
     */
    private $check;

    /**
     * @var string
     *
     * @ORM\Column(name="convention_code", type="string", nullable=true)
     */
    private $conventionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_type", type="string", nullable=true)
     */
    private $transactionType;

    /**
     * @var string
     *
     * @ORM\Column(name="product_type", type="string", nullable=true)
     */
    private $productType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="first_transaction", type="boolean", nullable=false)
     */
    private $isFirstPayment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inserted_ts", type="datetime", nullable=true)
     */
    private $insertedTs;

    public function __construct()
    {
        $this->insertedTs = date_create(date('Y-m-d H:i:s'));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getContractId()
    {
        return $this->contract->getId();
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int
     * @return Transactions
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutcome()
    {
        return $this->outcome;
    }

    /**
     * @param \DateTime
     * @return Transactions
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setCodAut($codAut)
    {
        $this->codAut = $codAut;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setCheck($check)
    {
        $this->check = $check;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setConventionCode($conventionCode)
    {
        $this->conventionCode = $conventionCode;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * @param string
     * @return Transactions
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * @var Contracts
     * @return Transactions
     */
    public function setContract(Contracts $contract)
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * @return Contracts
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @var boolean
     * @return Transactions
     */
    public function setIsFirstPayment($isFirstPayment)
    {
        $this->isFirstPayment = $isFirstPayment;

        return $this;
    }
}
