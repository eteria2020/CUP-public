<?php

namespace Cartasi\Entity\Repository;

use SharengoCore\Entity\Customers;

class ContractsRepository extends \Doctrine\ORM\EntityRepository
{
    public function findValidContractByCustomer(Customers $customer)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT c FROM \Cartasi\Entity\Contracts c '.
            'JOIN c.transactions t '.
            'WHERE c.customer = :customerId '.
            'AND t.isFirstPayment = TRUE '.
            'AND t.outcome = \'OK\''
        );
        $query->setParameter('customerId', $customer->getId());
        return $query->getOneOrNullResult();
    }
}
