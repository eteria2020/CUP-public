<?php

namespace Cartasi\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contracts
 *
 * @ORM\Table(name="contacts")
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
     * @var \Customers
     *
     * @ORM\ManyToOne(targetEntity="Customers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="pan", type="text", length=19, nullable=true)
     */
    private $pan;

    /**
     * @var string
     *
     * @ORM\Column(name="pan_expiry", type="text", length=6, nullable=true)
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
}
