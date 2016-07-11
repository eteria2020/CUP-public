<?php

namespace Application\Listener;

use MvLabsDriversLicenseValidation\Response;
use SharengoCore\Entity\Customers;
use SharengoCore\Exception\CustomerNotFoundException;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\DriversLicenseValidationService;

use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;

final class DriversLicensePostValidationLogger implements SharedListenerAggregateInterface
{
    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var DriversLicenseValidationService
     */
    private $validationService;

    /**
     * @param CustomersService $customersService
     * @param DriversLicenseValidationService $validationService
     */
    public function __construct(
        CustomersService $customersService,
        DriversLicenseValidationService $validationService
    ) {
        $this->customersService = $customersService;
        $this->validationService = $validationService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'MvLabsDriversLicenseValidation\Job\ValidationJob',
            'validDriversLicense',
            [$this, 'validDriversLicense']
        );

        $this->listeners[] = $events->attach(
            'MvLabsDriversLicenseValidation\Job\ValidationJob',
            'unvalidDriversLicense',
            [$this, 'unvalidDriversLicense']
        );
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param EventInterface $e
     */
    public function validDriversLicense(EventInterface $e)
    {
        $args = $e->getParam('args');
        $response = $e->getParam('response');

        $line = $this->csvLine($args, $response);

        $this->writeToCsv($line);

        $this->registerValidation($response, $args['email']);
    }

    /**
     * @param EventInterface $e
     */
    public function unvalidDriversLicense(EventInterface $e)
    {
        $args = $e->getParam('args');
        $response = $e->getParam('response');

        $line = $this->csvLine($args, $response);

        $this->writeToCsv($line);

        $this->registerValidation($response, $args['email']);
    }

    /**
     * @param mixed[] $args
     * @param Response $response
     * @return mixed[]
     */
    private function csvLine($args, $response)
    {
        return [
            $args['email'],
            $args['driverLicenseName'],
            $args['driverLicenseSurname'],
            $args['driverLicense'],
            $args['taxCode'],
            $args['birthDate']['date'],
            $args['birthCountry'],
            $args['birthCountryMCTC'],
            $args['birthProvince'],
            $args['birthTown'],
            $response->valid(),
            $response->code(),
            $response->message()
        ];
    }

    /**
     * @param mixed[] $line
     */
    private function writeToCsv($line)
    {
        $file = __DIR__ . '/../../../../../data/log/driversLicense.log';

        $fp = fopen($file, 'a');
        fputcsv($fp, $line);

        fclose($fp);
    }

    /**
     * @param Response $response
     * @param string $email
     * @throws CustomerNotFoundException
     */
    private function registerValidation(Response $response, $email)
    {
        $customer = $this->customersService->findOneByEmail($email);

        if ($customer instanceof Customers) {
            $this->validationService->addFromResponse($customer, $response);
        } else {
            throw new CustomerNotFoundException('Email: ' . $email);
        }
    }
}
