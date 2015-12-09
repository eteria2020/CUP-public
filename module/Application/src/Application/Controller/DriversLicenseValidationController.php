<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;

use MvLabsDriversLicenseValidation\Service\EnqueueValidationService;
use Zend\Mvc\Controller\AbstractActionController;

class DriversLicenseValidationController extends AbstractActionController
{
    /**
     * @var EnqueueValidationService $enqueueValidationService
     */
    private $enqueueValidationService;

    /**
     * @var CustomersService $customersService
     */
    private $customersService;

    public function __construct(
        EnqueueValidationService $enqueueValidationService,
        CustomersService $customersService
    ) {
        $this->enqueueValidationService = $enqueueValidationService;
        $this->customersService = $customersService;
    }

    public function validateDriversLicenseAction()
    {
        $customers = $this->customersService->getListCustomers();

        foreach ($customers as $customer) {
            $birthDate = $customer->getBirthDate() ? $customer->getBirthDate()->format('Y-m-d') : '0000-00-00';

            $data = [
                'name' => $customer->getName(),
                'surname' => $customer->getSurname(),
                'email' => $customer->getEmail(),
                'driverLicense' => $customer->getDriverLicense(),
                'taxCode' => $customer->getTaxCode(),
                'birthDate' => ['date' => $birthDate],
                'birthCountry' => $customer->getBirthCountry(),
                'birthProvince' => $customer->getBirthProvince(),
                'birthTown' => $customer->getBirthTown()
            ];

            $this->enqueueValidationService->validateDriversLicense($data);
        }
    }
}
