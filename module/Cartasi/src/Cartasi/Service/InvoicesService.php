<?php

namespace SharengoCore\Service;

use SharengoCore\Entity\Repository\InvoicesRepository;

class InvoicesService

    /** @var InvoicesRepository */
    private $invoicesRepository;

    /**
     * @param EntityRepository $invoicesRepository
     */
    public function __construct($invoicesRepository)
    {
        $this->invoicesRepository = $invoicesRepository;
    }

    /**
     * @return mixed
     */
    public function getListInvoices()
    {
        return $this->invoicesRepository->findAll();
    }

    /**
     * @return mixed
     */
    public function getCustomersInvoicesFirstPayment($customer)
    {
        return $this->invoicesRepository->findByCustomerFirstPayment($customer);
    }
}
