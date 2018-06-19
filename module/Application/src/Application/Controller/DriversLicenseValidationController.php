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
                'birthTown' => $customer->getBirthTown(),
                'language' => $customer->getLanguage()
            ];

            $this->enqueueValidationService->validateDriversLicense($data);
        }
    }

    /**
     * Utility to check a set of driver licence by user id.
     * 
     * By command line:
     * sudo php public/index.php validate drivers licenses by customer id 121935,121933,121928
     */
    public function validateDriversLicenseByCustomerIdAction()
    {
        $customers = array();

        $listOfCustomerId = trim($this->getRequest()->getParam('listOfCustomerId'));
        //$listOfCustomerId = '121935,121933,121928,121926,121923,121919,121918,121916,121913,121912,121911,121909,121908,121907,121906,121903,121899,121895,121894,121893,121892,121889,121888,121887,121886,121883,121878,121873,121866,121862,121861,121860,121857,121856,121855,121854,121853,121852,121851,121850,121849,121848,121847,121845,121844,121843,121842,121840,121838,121837,121836,121832,121829,121828,121826,121825,121824,121821,121820,121817,121816,121815,121814,121812,121807,121804,121802,121801,121800,121799,121793,121792,121791,121788,121787,121784,121782,121781,121778,121777,121776,121773,121761,121749,121746';
        //$listOfCustomerId = '67056,57079,53251,46284,33951,23896,13068,11662,11661,11644,11636,11629,11597,11587,2552';

        foreach(explode(',',$listOfCustomerId) as $customerId) {
            $customer = $this->customersService->findById(trim($customerId));
            if ($customer instanceof \SharengoCore\Entity\Customers) {
                array_push($customers, $customer);
            }
        }

        foreach ($customers as $customer) {
            $data = [
                'email' => $customer->getEmail(),
                'driverLicense' => $customer->getDriverLicense(),
                'taxCode' => $customer->getTaxCode(),
                'driverLicenseName' => $customer->getDriverLicenseName(),
                'driverLicenseSurname' => $customer->getDriverLicenseSurname(),
                'birthDate' => ['date' => $customer->getBirthDate()->format('Y-m-d')],
                'birthCountry' => $customer->getBirthCountry(),
                'birthProvince' => $customer->getBirthProvince(),
                'birthTown' => $customer->getBirthTown()
            ];

            $this->enqueueValidationService->validateDriversLicense($data);
        }
    }
}
