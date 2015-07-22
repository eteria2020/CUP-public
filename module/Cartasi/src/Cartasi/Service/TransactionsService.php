<?php

namespace Cartasi\Service;

use Cartasi\Entity\Repository\TransactionsRepository;

class TransactionsService
{

    /** @var TransactionsRepository */
    private $transactionsRepository;

    /**
     * @param EntityRepository $transactionsRepository
     */
    public function __construct($transactionsRepository)
    {
        $this->transactionsRepository = $transactionsRepository;
    }

    /**
     * @return mixed
     */
    public function getListTransactions()
    {
        return $this->transactionsRepository->findAll();
    }
}
