<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\InvoicesService;
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

        // get customers with first payment completed

        // check if invoice exists

        // if not exists create invoice

    }

    private function writeToConsole($string)
    {
        if ($this->verbose) {
            fwrite(STDOUT, $string);
        }
    }

}
