<?php

namespace Cartasi\Entity;

/**
 * Transactions
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="SharengoCore\Entity\Repository\TransactionsRepository")
 */
class Transactions
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="transactions_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", $type="integer", nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", $type="string", length=3, nullable=false)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text", nullable=true)
     */
    private $email;

    /**
     * @var \Contracts
     *
     * @ORM\ManyToOne(targetEntity="Contrancts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contract_id", referencedColumnName="id")
     * })
     */
    private $contract;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
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
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $codAut;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $check;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $conventionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $tranactionType;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $productType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inserted_ts", type="datetime", nullable=true)
     */
    private $insertedTs;

    public function __construct()
    {
        $this->insertedTs = date('Y-m-d h:i:s');
    }
}
