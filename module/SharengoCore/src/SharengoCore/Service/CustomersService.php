<?php

namespace SharengoCore\Service;

class CustomersService
{

    private $entityManager;

    private $clientRepository;

    /**
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;

        $this->clientRepository = $this->entityManager->getRepository('\SharengoCore\Entity\Customers');
    }

    /**
     * @return mixed
     */
    public function getListCustomers()
    {
        return $this->clientRepository->findAll();
    }

    public function getUserByEmailPassword($s_username, $s_password)
    {
        return $this->clientRepository->getUserByEmailPassword($s_username, $s_password);

    }
    public function findByEmail($email)
    {
        return $this->clientRepository->findBy([
            'email' => $email
        ]);
    }

    public function findByTaxCode($taxCode)
    {
        return $this->clientRepository->findBy([
            'taxCode' => $taxCode
        ]);
    }

    public function findByDriversLicense($driversLicense)
    {
        return $this->clientRepository->findByCI('driverLicense', $driversLicense);
    }
}
