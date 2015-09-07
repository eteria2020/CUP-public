<?php

namespace Application\Controller;

use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\TripsService;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;

class ExportRegistriesController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        EntityManager $entityManager,
        CustomersService $customersService,
        InvoicesService $invoicesService,
        TripsService $tripsService,
        Logger $logger
    ) {
        $this->entityManager = $entityManager;
        $this->customersService = $customersService;
        $this->invoicesService = $invoicesService;
        $this->tripsService = $tripsService;
        $this->logger = $logger;
    }

    // TODO
    // throw exception if customer does not have card
    // add IVA to Invoices (in json)
    // create file (find out where)
    public function exportRegistriesAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $noCustomers = $request->getParam('no-customers') || $request->getParam('c');
        $noInvoices = $request->getParam('no-invoices') || $request->getParam('i');
        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // Create the output file
        $file = fopen("export.txt", "w");
        $fileContent = "";

        // Generate customers registries
        if (!$noCustomers) {
            $this->logger->log("Exporting customers...\n\n");
            $customers = $this->customersService->getCustomersForExport();
            foreach ($customers as $customer) {
                $this->logger->log("Exporting customer: " . $customer->getId() . "\n");
                $record = $this->customersService->getExportDataForCustomer($customer);
                $fileContent .= $record . "\n";
            }
            $this->logger->log("\n");
        }

        // Export invoices registries
        if (!$noInvoices) {
            $this->logger->log("Exporting invoices...\n\n");
            $invoices = $this->invoicesService->getInvoicesForExport();
            foreach ($invoices as $invoice) {
                $this->logger->log("Exporting invoice: " . $invoice->getId() . "\n");
                $record = $this->invoicesService->getExportDataForInvoice($invoice);
                $fileContent .= $record . "\n";
            }
        }

        if (!$dryRun) {
            fwrite($file, $fileContent);
        }

        fclose($file);
        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
