<?php

namespace SharengoCore\Service;

use SharengoCore\Entity\Repository\TransactionsRepository;
use SharengoCore\Entity\Trips;

class TripsService

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
