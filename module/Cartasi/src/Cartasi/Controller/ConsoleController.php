<?php

namespace Cartasi\Controller;

use SharengoCore\Service\CustomersService;
use Cartasi\Service\InvoicesService;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
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
     * @var boolean defines verbosity
     */
    private $verbose;

    public function __construct(
        CustomersService $customersService,
        InvoicesService $invoicesService
    ) {
        $this->customersService = $customersService;
        $this->invoicesService = $invoicesService;
    }

    public function invoiceRegistrations()
    {
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request;
        $this->verbose = $request->getParam('verbose') || $request->getParam('v');
        $this->writeToConsole("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
        $invoicesCreated = 0;

        // get customers with first payment completed
        $customers = $this->customersService->getCustomersFirstPaymentCompleted();
        $this->writeToConsole("Retrieved customers: " . count($customers) . "\n\n");

        // check if customer has invoice for first payment
        foreach ($customers as $customer) {
            $this->writeToConsole('Customer: ' . $customer->getId() . "\n");
            $invoice = $this->invoicesService->getCustomersInvoicesFirstPayment($customer);

            // if there is no invoice for the first payment
            if($invoice == null || empty($invoice)) {
                $this->writeToConsole("Invoice not found\n");
                $this->invoicesService->createInvoiceForFirstPayment($customer);
                $this->writeToConsole("Invoice created\n\n");
                $invoicesCreated ++;
            } else {
                $this->writeToConsole("Invoice found: " . $invoice->getId() . "\n\n");
            }
        }
        $this->writeToConsole("Created " . $invoicesCreated . " invoices\n\n");
        $this->writeToConsole("\nDone\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

    }

    private function writeToConsole($string)
    {
        if ($this->verbose) {
            fwrite(STDOUT, $string);
        }
    }

}
