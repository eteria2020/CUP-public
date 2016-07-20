<?php

namespace Application\Controller;

use MvLabsDriversLicenseValidation\Response\Response;
use SharengoCore\Entity\Customers;
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
     * @var mixed[]
     */
    private $validationConfig;

    /**
     * Specifies wether validations should be saved to db
     * @var boolean
     */
    private $dryRun;

    /**
     * Specifies wether validation should be generated from data instead of from
     * Response object
     * @var boolean
     */
    private $useData;

    /**
     * @param CustomersService $customersService
     * @param DriversLicenseValidationService $validationService
     * @param Logger $logger
     * @param mixed[] $validationConfig
     */
    public function __construct(
        CustomersService $customersService,
        DriversLicenseValidationService $validationService,
        Logger $logger,
        array $validationConfig
    ) {
        $this->customersService = $customersService;
        $this->validationService = $validationService;
        $this->logger = $logger;
        $this->validationConfig = $validationConfig;
    }

    /**
     * Available params are:
     *     [--dry-run|-d] (does not save entities to db)
     *     [--use-data] (does not create Response to generate Validation)
     *     [--id=] (id of customer for which to create validation)
     *     [--email=] (email of customer of which to create validation.
     *                 overridden by [--id=])
     *     [--valid=] (defaults to true)
     *     [--code=] (defaults to 0)
     *     [--msg=] (defaults to PATENTE VALIDA)
     */
    public function testValidationAction()
    {
        // Setup logger
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        // Get/Set params
        $request = $this->getRequest();
        $this->dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $this->useData = $request->getParam('use-data');
        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // Get params
        $id = $request->getParam('id');
        $email = $request->getParam('email');
        $valid = $request->getParam('valid');
        if (!is_null($valid)) {
            if ($valid == 'false') {
                $valid = false;
            } else {
                $valid = true;
                if ($valid != 'true') {
                    $this->logger->log('Could not read [--valid=] param. Set to default (true)\n\n');
                }
            }
        } else {
            $valid = true;
        }
        $code = is_null($request->getParam('code')) ? 0 : $request->getParam('code');
        $message = is_null($request->getParam('message')) ? 'PATENTE VALIDA' : $request->getParam('message');

        // Get Customer
        $customer = null;
        if (is_null($id)) {
            if (is_null($email)) {
                $this->logger->log("No id and no email specified. One of the two must be present\n\n");
            } else {
                $customer = $this->customersService->findOneByEmail($email);
            }
        } else {
            $customer = $this->customersService->findById($id);
        }

        // Check Customer
        if (!$customer instanceof Customers) {
            $this->logger->log("Customer not found\n\n");
        } else {
            $this->logger->log("Customer found\n\n");

            if ($this->useData) {
                $validation = $this->validationService->addFromData(
                    $customer,
                    $valid,
                    $code,
                    $message,
                    ['test validation'],
                    true,
                    !$this->dryRun
                );
            } else {
                // Generate Response
                $response = new Response($valid, $code, $message);
                $this->logger->log("Response created:\n");
                $this->logger->log("valid: " . ($response->valid() ? 'yes' : 'no') . "\n");
                $this->logger->log("code: " . $response->code() . "\n");
                $this->logger->log("message: " . $response->message() . "\n\n");

                // Generate Validation
                $validation = $this->validationService->addFromResponse(
                    $customer,
                    $response,
                    ['test validation'],
                    true,
                    !$this->dryRun
                );
            }

            $this->logger->log("Validation created" . ($this->dryRun ? '' : " with id: " . $validation->getId()) . "\n\n");
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    /**
     * Available params are:
     *     [--dry-run|-d] (does not save entities to db)
     *     [--one|-o] (stops after at least one validation is generated)
     */
    public function importValidationsAction()
    {
        // Setup logger
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        // Get/Set params
        $request = $this->getRequest();
        $this->dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $one = $request->getParam('one') || $request->getParam('o');
        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // Get data from logs
        $fp = fopen($this->validationConfig['logDir'], 'r');
        $this->logger->log('Opened file: ' . $this->validationConfig['logDir'] . "\n\n");
        for ($row = 1; ($data = fgetcsv($fp)) !== FALSE; $row++) {

            // Get customer
            $customer = $this->customersService->findOneByEmail($data[0]);
            if (!($customer instanceof Customers)) {
                $this->logger->log('Customer not found with email: ' . $data[0] . "\n\n");
                continue;
            }
            $this->logger->log('Customer: ' . $customer->getId() . "\n");

            // Generate Response
            $response = new Response($data[10], $data[11], $data[12]);
            $this->logger->log(
                'Generated Response with: ' .
                'valid=' . ($response->valid() ? 'true' : 'false') .
                ' code=' . $response->code() .
                ' message=' . $response->message() . "\n");

            // Generate validation
            $validation = $this->validationService->addFromResponse(
                $customer,
                $response,
                $data,
                true,
                !$this->dryRun
            );
            $this->logger->log("Validation created" . ($this->dryRun ? '' : " with id: " . $validation->getId()) . "\n\n");

            if ($one) {
                $this->logger->log("Stopped after one successful row\n\n");
                break;
            }
        }
        fclose($fp);

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
