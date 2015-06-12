<?php

namespace Cartasi\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contracts
 *
 * @ORM\Table(name="contracts")
 * @ORM\Entity(repositoryClass="SharengoCore\Entity\Repository\ContractsRepository")
 */
class Contracts
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="contracts_id_seq", allocationSize=1, initialValue=1)
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
     * @var string
     *
     * @ORM\Column(name="pan", type="string", length=19, nullable=true)
     */
    private $pan;

    /**
     * @var string format aaaamm
     *
     * @ORM\Column(name="pan_expiry", type="string", length=6, nullable=true)
     */
    private $panExpiry;

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

    /**
     * verifies if the pan is expired
     *
     * @return boolean
     */
    public function isExpired()
    {

    }

    /**
     * retrieves the email of the customer
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->customer->getEmail();
    }
}
