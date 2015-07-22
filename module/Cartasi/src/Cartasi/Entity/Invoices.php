<?php

namespace Cartasi\Entity;

use Doctrine\ORM\Mapping as ORM;
use SharengoCore\Entity\Invoices;

/**
 * Invoices
 *
 * @ORM\Table(name="invoices")
 * @ORM\Entity(repositoryClass="Cartasi\Entity\Repository\InvoicesRepository")
 */
class Invoices
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="invoices_id_seq", allocationSize=1, initialValue=0)
     */
    private $id;

    /**
     * @var \SharengoCore\Entity\Customers
     *
     * @ORM\ManyToOne(targetEntity="SharengoCore\Entity\Customers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    private $customer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="generated_ts", type="datetime", nullable=false)
     */
    private $generatedTs;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="json_array", nullable=false)
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="version", type="integer", nullable=false)
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_type", type="string", nullable=false)
     */
    private $invoiceType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="emitted_ts", type="datetime", nullable=false)
     */
    private $emittedTs;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer", nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_number", type="string", nullable=false)
     */
    private $invoiceNumber;

    public function __construct()
    {
        $this->generatedTs = date_create(date('Y-m-d H:i:s'));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \SharengoCore\Entity\Customers
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @var \SharengoCore\Entity\Customers
     * @return Invoices
     */
    public function setCustomer(Customers $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getGeneratedTs()
    {
        return $this->generatedTs;
    }

    /**
     * @var \DateTime
     * @return Invoices
     */
    public function setGeneratedTs($generatedTs)
    {
        $this->generatedTs = $generatedTs;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @var mixed
     * @return Invoices
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @var integer
     * @return Invoices
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceType()
    {
        return $this->invoiceType;
    }

    /**
     * @var string
     * @return Invoices
     */
    public function setInvoiceType($invoiceType)
    {
        $this->invoiceType = $invoiceType;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEmittedTs()
    {
        return $this->emittedTs;
    }

    /**
     * @var \DateTime
     * @return Invoices
     */
    public function setEmittedTs($emittedTs)
    {
        $this->emittedTs = $emittedTs;
        return $this;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @var integer
     * @return Invoices
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @var string
     * @return Invoices
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }
}
