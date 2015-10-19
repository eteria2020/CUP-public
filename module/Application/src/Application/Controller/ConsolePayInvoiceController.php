<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Listener\PaymentEmailListener;
use SharengoCore\Listener\NotifyCustomerPayListener;
use Cartasi\Service\CartasiCustomerPaymentsRetry;
use Cartasi\Exception\WrongPaymentException;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\EventManager\EventInterface;

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
     * @var NotifyCustomerPayListener
     */
    private $notifyCustomerPayListener;

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
     * @param TripPaymentsService $tripPaymentsService
     * @param PaymentsService $paymentsService
     * @param InvoicesService $invoicesService
     * @param Logger $logger
     * @param PaymentEmailListener $paymentEmailListener
     * @param NotifyCustomerPayListener $notifyCustomerPayListener
     */
    public function __construct(
        TripPaymentsService $tripPaymentsService,
        PaymentsService $paymentsService,
        InvoicesService $invoicesService,
        Logger $logger,
        PaymentEmailListener $paymentEmailListener,
        NotifyCustomerPayListener $notifyCustomerPayListener
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->paymentsService = $paymentsService;
        $this->invoicesService = $invoicesService;
        $this->logger = $logger;
        $this->paymentEmailListener = $paymentEmailListener;
        $this->notifyCustomerPayListener = $notifyCustomerPayListener;
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
        $this->getEventManager()->getSharedManager()->attachAggregate($this->notifyCustomerPayListener);

        foreach ($tripsPayments as $tripPayment) {
            try {
                $this->logger->log("Processing payment for trip payment " . $tripPayment->getId() . "\n");
                $this->paymentsService->tryPayment(
                    $tripPayment,
                    $this->avoidEmails,
                    $this->avoidCartasi,
                    $this->avoidPersistance
                );
            } catch (WrongPaymentException $e) {
                // if we are not able to process a payment we skip the followings
                break;
            }
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
