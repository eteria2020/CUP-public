<?php

namespace Cartasi\Entity\Repository;

class InvoicesRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param \SharengoCore\Entity\Customers $customer
     * @return mixed
     */
    public function findByCustomerFirstPayment($customer)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT t FROM \Cartasi\Entity\Invoices i
            WHERE i.customer = :customerId
            AND i.isFirstPayment = true";

        $query = $em->createQuery($dql);
        $query->setParameter('customerId', $customer->getId());
        return $query->getResult();
    }
}
