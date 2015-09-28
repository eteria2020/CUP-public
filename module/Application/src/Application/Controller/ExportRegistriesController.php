<?php

namespace Application\Controller;

use Application\Exception\MissingCardFromCustomerException;

use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\TripsService;
use SharengoCore\Entity\Invoices;

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

    /**
     * Writes a file (TODO define path) in which all data to be
     * exported is written.
     * Available params are:
     *     -d (does not write to file)
     *     -c (does not export customers data)
     *     -i (does not export invoices data)
     */
    public function exportRegistriesAction()
    {
        // Setup logger
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        // Get params
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $noCustomers = $request->getParam('no-customers') || $request->getParam('c');
        $noInvoices = $request->getParam('no-invoices') || $request->getParam('i');
        $ignoreExceptions = $request->getParam('ignore-exceptions') || $request->getParam('e');
        $all = $request->getParam('all') || $request->getParam('a');
        $exceptionThrown = false;
        $path = "data/export/";
        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        /*
        // Generate customers registries
        if (!$noCustomers) {
            try {
                $this->logger->log("Exporting customers...\n\n");
                $fileContentCustomers = "";
                $customers = $this->customersService->getCustomersForExport();
                foreach ($customers as $customer) {
                    $this->logger->log("Exporting customer: " . $customer->getId() . "\n");
                    try {
                        $record = $this->customersService->getExportDataForCustomer($customer);
                    } catch(MissingCardFromCustomerException $ex) {
                        if ($ignoreExceptions) {
                            $this->logger->log($ex->getMessage() . "\n");
                            continue;
                        } else {
                            throw $ex;
                        }
                    }
                    $fileContentCustomers .= $record . "\r\n";
                }
                $this->logger->log("\n");
                if (!$dryRun) {
                    $fileCustomers = fopen("exportCustomers.txt", "w");
                    fwrite($fileCustomers, $fileContentCustomers);
                    fclose($fileCustomers);
                }
            } catch(MissingCardFromCustomerException $e) {
                $this->logger->log("\n" . $ex->getMessage() . "\n");
                $exceptionThrown = true;
            }
        }
        */

        $invoicesByDate = $this->invoicesService->getInvoicesForExport();
        $this->logger->log("Retrieved invoices\n");
        foreach ($invoicesByDate as $invoices) {
            $this->logger->log("\nParsing invoices for date: " . $invoices[0]->getDateTimeDate()->format('Y-m-d') . "\n");
            $invoicesEntry = '';
            $customersEntry = '';
            foreach ($invoices as $invoice) {
                if (!$noInvoices) {
                    $this->logger->log("Exporting invoice: " . $invoice->getId() . "\n");
                    $invoicesEntry .= $this->invoicesService->getExportDataForInvoice($invoice) . "\r\n";
                }
                if (!$noCustomers && $invoice->getType() == Invoices::TYPE_FIRST_PAYMENT) {
                    $this->logger->log("Exporting customer: " . $invoice->getCustomer()->getId() . "\n");
                    $customersEntry .= $this->customersService->getExportDataForCustomer($invoice->getCustomer()) . "\r\n";
                }
            }
            if (!$dryRun && !$noInvoices && $invoicesEntry !== '') {
                $this->logger->log("Writing invoices to file for the day\n");
                $fileInvoices = fopen($path . $invoices[0]->getDateTimeDate()->format('Y-m-d') . "-fatture.txt", 'w');
                fwrite($fileInvoices, $invoicesEntry);
                fclose($fileInvoices);
            }
            if (!$dryRun && !$noCustomers && $customersEntry !== '') {
                $this->logger->log("Writing customers to file for the day\n");
                $fileCustomers = fopen($path . $invoices[0]->getDateTimeDate()->format('Y-m-d') . "-clienti.txt", 'w');
                fwrite($fileCustomers, $customersEntry);
                fclose($fileCustomers);
            }
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
