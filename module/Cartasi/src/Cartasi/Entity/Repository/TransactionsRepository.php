<?php

namespace Cartasi\Entity\Repository;

use SharengoCore\Entity\Customers;

class TransactionsRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOneWithCompletedFirstPayment(Customers $customer)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT t FROM \Cartasi\Entity\Transactions t '.
            'JOIN t.contract c '.
            'WHERE c. = :customer '.
            'AND t.isFirstPayment = TRUE '.
            'AND t.outcome = "OK"'
        );
        return $query->getSingleScalarResult() > 0;
    }
}
