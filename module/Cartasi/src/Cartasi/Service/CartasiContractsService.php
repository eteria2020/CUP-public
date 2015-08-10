<?php

namespace Cartasi\Service;

use Cartasi\Entity\Repository\ContractsRepository;
use SharengoCore\Entity\Customers;

class CartasiContractsService
{
    /**
     * @var ContractsRepository
     */
    private $contractsRepository;

    public function __construct(ContractsRepository $contractsRepository)
    {
        $this->contractsRepository = $contractsRepository;
    }

    /**
     * retrieves a contract for which the first payment was mad for the given
     * customer
     * if no contract is found, null is returned
     *
     * @param Customers $customer
     * @return int|null
     */
    public function getCartasiContractNumber(Customers $customer)
    {
        $contract = $this->contractsRepository->findValidContractByCustomer($customer);

        if (!$contract) {
            return null;
        }

        return $contract->getId();
    }
}
