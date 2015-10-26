<?php

namespace Application\Controller;

use SharengoCore\Service\CustomerBonusService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class GeneratePackageInvoicesController extends AbstractActionController
{
    /**
     * @var CustomerBonusService
     */
    private $bonuses;

    /**
     * @var Logger $logger
     */
    private $logger;

    public function __construct(
        CustomerBonusService $bonuses,
        Logger $logger
    ) {
        $this->bonuses = $bonuses;
        $this->logger = $logger;
    }

    public function generatepackageInvoicesAction()
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

        $bonusPayments = $this->bonuses->getBonusPaymentsForInvoice();
        $this->logger->log("Processing payments for " . count($bonusPayments) . " bonus package\n");

        foreach ($bonusPayments as $bonusPayment) {
            $this->logger->log("Processing payment for bonus packages payment " . $bonusPayment->getId() . "\n");
            $this->bonuses->generateInvoice($bonusPayment, !$this->dryRun);
        }

        $this->logger->log("Done processing payments\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
