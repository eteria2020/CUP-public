<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\DriversLicenseValidationService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class ImportDriversLicenseValidationsController extends AbstractActionController
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
     * @var Logger
     */
    private $logger;

    /**
     * Specifies wether validations should be saved to db
     * @var boolean
     */
    private $dryRun;

    /**
     * @param CustomersService $customersService
     * @param DriversLicenseValidationService $validationService
     * @param Logger $logger
     */
    public function __construct(
        CustomersService $customersService,
        DriversLicenseValidationService $validationService,
        Logger $logger
    ) {
        $this->customersService = $customersService;
        $this->validationService = $validationService;
        $this->logger = $logger;
    }

    /**
     * Available params are:
     *     -d (does not generate files)
     */
    public function testValidationAction()
    {
        // Setup logger
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        // Get/Set params
        $request = $this->getRequest();
        $this->dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $email = '';
        $valid = false;
        $code = -1;
        $message = 'Gateway not reachable';

        // get Customer
        $customer = $this->customersService->findOneByEmail($email);
        if (!$customer instanceof Customers) {
            $this->logger->log("Customer not found with email: " . $email . "\n\n");
        } else {
            $this->logger->log("Customer found with email: " . $email . "\n\n");

            // generate Response
            $response = new Response($valid, $code, $message);
            $this->logger->log("Response created:\n");
            $this->logger->log("valid: " . ($response->valid() ? 'yes' : 'no') . "\n");
            $this->logger->log("code: " . $response->code() . "\n");
            $this->logger->log("message: " . $response->message() . "\n\n");

            // generate Validation
            $validation = $this->validationService->addFromResponse(
                $customer,
                $response,
                !$dryRun
            );
            $this->logger->log("Validation created" . ($dryRun ? '' : " with id: " . $validation->getId()) . "\n\n");
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
