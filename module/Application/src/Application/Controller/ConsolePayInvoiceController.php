<?php

namespace Application\Controller;

use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Service\ProcessPaymentsService;
use Cartasi\Exception\WrongPaymentException;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;

class ConsolePayInvoiceController extends AbstractActionController
{
    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var TripCostService
     */
    private $tripCostService;

    /**
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ProcessPaymentsService
     */
    private $processPaymentsService;

    /**
     * @var boolean
     */
    private $avoidEmails;

    /**
     * @var boolean
     */
    private $avoidCartasi;

    /**
     * @var boolean
     */
    private $avoidPersistance;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param TripPaymentsService $tripPaymentsService
     * @param InvoicesService $invoicesService
     * @param Logger $logger
     * @param ProcessPaymentsService $processPaymentsService
     */
    public function __construct(
        TripPaymentsService $tripPaymentsService,
        InvoicesService $invoicesService,
        Logger $logger,
        ProcessPaymentsService $processPaymentsService,
        EntityManager $entityManager
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->invoicesService = $invoicesService;
        $this->logger = $logger;
        $this->processPaymentsService = $processPaymentsService;
        $this->processPaymentsService->setLogger($this->logger);
        $this->entityManager = $entityManager;
    }

    public function payInvoiceAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $this->avoidEmails = $request->getParam('no-emails') || $request->getParam('e');
        $this->avoidCartasi = $request->getParam('no-cartasi') || $request->getParam('c');
        $this->avoidPersistance = $request->getParam('no-db') || $request->getParam('d');

        $this->processPayments();

        // clear the entity manager cache
        $this->entityManager->clear();

        $this->generateInvoices();
    }

    private function processPayments()
    {
        $this->logger->log("\nStarted processing payments\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $tripPayments = $this->tripPaymentsService->getTripPaymentsForPayment();
        $this->logger->log("Processing payments for " . count($tripPayments) . " trips\n");

        $this->processPaymentsService->processPayments(
            $tripPayments,
            $this->avoidEmails,
            $this->avoidCartasi,
            $this->avoidPersistance
        );

        $this->logger->log("Done processing payments\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    private function generateInvoices()
    {
        $this->logger->log("\nStarted generating invoices\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // get all trip_payments without invoice
        $tripPayments = $this->tripPaymentsService->getTripPaymentsNoInvoiceGrouped();
        $this->logger->log("Generating invoices for " . count($tripPayments) . "trips\n");

        // generate the invoices
        $invoices = $this->invoicesService->createInvoicesForTrips($tripPayments, !$this->avoidPersistance);

        $this->logger->log("Done generating invoices\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
