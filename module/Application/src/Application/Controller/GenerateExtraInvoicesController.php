<?php

namespace Application\Controller;

use SharengoCore\Service\ExtraPaymentsService;
use SharengoCore\Service\ExtraPaymentsSearchService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class GenerateExtraInvoicesController extends AbstractActionController
{
    /**
     * @var ExtraPaymentsService
     */
    private $extraPayments;

    /**
     * @var ExtraPaymentsSearchService
     */
    private $extraPaymentsSearch;

    /**
     * @var bool
     */
    private $dryRun;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param ExtraPaymentsService $extraPayments
     * @param ExtraPaymentsSearchService $extraPaymentsSearch
     * @param Logger $logger
     */
    public function __construct(
        ExtraPaymentsService $extraPayments,
        ExtraPaymentsSearchService $extraPaymentsSearch,
        Logger $logger
    ) {
        $this->extraPayments = $extraPayments;
        $this->extraPaymentsSearch = $extraPaymentsSearch;
        $this->logger = $logger;
    }

    public function generateExtraInvoicesAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $this->dryRun = $request->getParam('dry-run') || $request->getParam('d');

        $this->generateInvoices();
    }

    private function generateInvoices()
    {
        $this->logger->log("\nStarted processing payments\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $extraPayments = $this->extraPaymentsSearch->getExtraPaymentsForInvoice();
        $this->logger->log("Processing payments for " . count($extraPayments) . " extras\n");

        foreach ($extraPayments as $extraPayment) {
            $this->logger->log("Processing payment for extra payment " . $extraPayment->getId() . "\n");
            $this->extraPayments->generateInvoice($extraPayment, !$this->dryRun);
        }

        $this->logger->log("Done processing payments\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
