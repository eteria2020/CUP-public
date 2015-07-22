<?php

namespace Application\Controller;

use SharengoCore\Service\TransactionsService;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{

    /**
     * @var TransactionsService
     */
    private $transactionsService;

    /**
     * @var boolean defines verbosity
     */
    private $verbose;

    public function __construct(TransactionsService $transactionsService)
    {
        $this->transactionsService = $transactionsService;
    }

    public function invoiceRegistrations()
    {
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request;
        $this->verbose = $request->getParam('verbose') || $request->getParam('v');

    }

    private function writeToConsole($string)
    {
        if ($this->verbose) {
            fwrite(STDOUT, $string);
        }
    }

}
