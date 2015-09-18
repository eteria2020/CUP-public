<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Listener\PaymentEmailListener;

use Zend\Mvc\Controller\AbstractActionController;

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
     * @var PaymentsService
     */
    private $paymentsService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var PaymentEmailListener
     */
    private $paymentEmailListener;

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
     * @param TripsService $tripsService
     * @param TripCostService $tripCostService
     * @param TripPaymentsService
     * @param PaymentsService
     * @param InvoicesService
     * @param Logger $logger
     */
    public function __construct(
        TripPaymentsService $tripPaymentsService,
        PaymentsService $paymentsService,
        InvoicesService $invoicesService,
        Logger $logger,
        PaymentEmailListener $paymentEmailListener
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->paymentsService = $paymentsService;
        $this->invoicesService = $invoicesService;
        $this->logger = $logger;
        $this->paymentEmailListener = $paymentEmailListener;
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
        $this->generateInvoices();
    }

    private function processPayments()
    {
        $this->logger->log("\nStarted processing payments\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $tripsPayments = $this->tripPaymentsService->getTripPaymentsForPayment();
        $this->logger->log("Processing payments for " . count($tripsPayments) . " trips\n");

        $this->getEventManager()->getSharedManager()->attachAggregate($this->paymentEmailListener);

        foreach ($tripsPayments as $tripPayment) {
            $this->logger->log("Processing payment for trip payment " . $tripPayment->getId() . "\n");
            $this->paymentsService->tryPayment(
                $tripPayment,
                $this->avoidEmails,
                $this->avoidCartasi,
                $this->avoidPersistance
            );
        }

        $this->getEventManager()->trigger('processPaymentsCompleted', $this, [
            'avoidEmails' => $this->avoidEmails
        ]);

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
