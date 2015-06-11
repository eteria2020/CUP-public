<?php

namespace SharengoCore\Entity;

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

    
}
