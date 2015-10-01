<?php

namespace Application\Controller;

use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Entity\Invoices;

use Zend\Mvc\Controller\AbstractActionController;

class ExportRegistriesController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $exportConfig;

    public function __construct(
        CustomersService $customersService,
        InvoicesService $invoicesService,
        Logger $logger,
        $exportConfig
    ) {
        $this->customersService = $customersService;
        $this->invoicesService = $invoicesService;
        $this->logger = $logger;
        $this->exportConfig = $exportConfig;
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
        $all = $request->getParam('all') || $request->getParam('a');
        $noFtp = $request->getParam('no-ftp') || $request->getParam('f');
        $testName = $request->getParam('test-name') || $request->getParam('t') ? 'test-' : '';
        $path = $this->exportConfig['path'];
        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $this->logger->log("Retrieving invoices...");
        $invoicesByDate = null;
        if ($all) {
            $this->logger->log("all...");
            $invoicesByDate = $this->invoicesService->getInvoicesWithCustomer();
        } else {
            $this->logger->log("for yesterday...");
            $invoicesByDate = $this->invoicesService->getInvoicesByDate(date_create('yesterday'));
        }
        $invoicesByDate = $this->invoicesService->groupByInvoiceDate($invoicesByDate);
        $this->logger->log(" Retrieved!\n");

        if (!$noFtp) {
            $this->logger->log("Connecting to ftp server... ");
            $ftp_server = $this->exportConfig['server'];
            $ftp_conn = ftp_connect($ftp_server) or die(" Could not connect to $ftp_server!\n");
            $login = ftp_login($ftp_conn, $this->exportConfig['name'], $this->exportConfig['password']);
            ftp_pasv($ftp_conn, true);
            $this->logger->log(" Connected!\n");
        }

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
                $fileName = $testName . "exportInvoices_" . $invoices[0]->getDateTimeDate()->format('Y-m-d') . ".txt";
                $fileInvoices = fopen($path . $fileName, 'w');
                fwrite($fileInvoices, $invoicesEntry);
                fclose($fileInvoices);
                if (!$noFtp) {
                    if (ftp_put($ftp_conn, $fileName, $path . $fileName, FTP_ASCII)) {
                        $this->logger->log("File uploaded successfully\n");
                    } else {
                        $this->logger->log("Error uploading file\n");
                    }
                }
            }
            if (!$dryRun && !$noCustomers && $customersEntry !== '') {
                $this->logger->log("Writing customers to file for the day\n");
                $fileName = $testName . "exportCustomers_" . $invoices[0]->getDateTimeDate()->format('Y-m-d') . ".txt";
                $fileCustomers = fopen($path . $fileName, 'w');
                fwrite($fileCustomers, $customersEntry);
                fclose($fileCustomers);
                if (!$noFtp) {
                    if (ftp_put($ftp_conn, $fileName, $path . $fileName, FTP_ASCII)) {
                        $this->logger->log("File uploaded successfully\n");
                    } else {
                        $this->logger->log("Error uploading file\n");
                    }
                }
            }
        }
        if (!$noFtp) {
            ftp_close($ftp_conn);
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
