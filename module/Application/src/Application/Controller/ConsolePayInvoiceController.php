<?php

namespace Application\Controller;

use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Service\ProcessPaymentsService;
use SharengoCore\Service\PaymentScriptRunsService;
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
     * @var PaymentScriptRunsService
     */
    private $paymentScriptRunsService;

    /**
     * @param TripPaymentsService $tripPaymentsService
     * @param InvoicesService $invoicesService
     * @param Logger $logger
     * @param ProcessPaymentsService $processPaymentsService
     * @param EntityManager $entityManager
     * @param PaymentScriptRunsService $paymentScriptRunsService
     */
    public function __construct(
        TripPaymentsService $tripPaymentsService,
        InvoicesService $invoicesService,
        Logger $logger,
        ProcessPaymentsService $processPaymentsService,
        EntityManager $entityManager,
        PaymentScriptRunsService $paymentScriptRunsService
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->invoicesService = $invoicesService;
        $this->logger = $logger;
        $this->processPaymentsService = $processPaymentsService;
        $this->processPaymentsService->setLogger($this->logger);
        $this->entityManager = $entityManager;
        $this->paymentScriptRunsService = $paymentScriptRunsService;
    }

    public function payInvoiceAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $this->avoidEmails = $request->getParam('no-emails') || $request->getParam('e');
        $this->avoidCartasi = $request->getParam('no-cartasi') || $request->getParam('c');
        $this->avoidPersistance = $request->getParam('no-db') || $request->getParam('d');

        if (!$this->paymentScriptRunsService->isRunning()) {
            $scriptId = $this->paymentScriptRunsService->scriptStarted();
            $this->processPayments();

            $this->paymentScriptRunsService->scriptEnded($scriptId);

            // clear the entity manager cache
            $this->entityManager->clear();

            //$this->generateInvoices();
        } else {
            $this->logger->log("\nError: Pay invoice is running\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
        }
    }

    /*
     * Re try to pay trips marked as "wrong payments" in last -2 days
     */
    public function retryWrongPaymentsAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        //$this->avoidEmails = $request->getParam('no-emails') || $request->getParam('e');
        $this->avoidEmails = true;  // force avoid send email during re try wong paiment
        $this->avoidCartasi = $request->getParam('no-cartasi') || $request->getParam('c');
        $this->avoidPersistance = $request->getParam('no-db') || $request->getParam('d');

        if (!$this->paymentScriptRunsService->isRunning()) {
            $scriptId = $this->paymentScriptRunsService->scriptStarted();

            $this->reProcessWrongPayments();

            $this->paymentScriptRunsService->scriptEnded($scriptId);
            $this->entityManager->clear();

            //$this->generateInvoices();
        } else {
            $this->logger->log(date_create()->format('y-m-d H:i:s') . ";ERR;retryWrongPaymentsAction;Error Retry: Pay invoice is running\n");
        }
    }

    private function processPayments()
    {
        $this->logger->log("\nStarted processing payments\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
        //$tripPayments = $this->tripPaymentsService->getTripPaymentsForPayment(null, '-40 days');
        $verify = $this->tripPaymentsService->getTripPaymentsForPaymentDetails('180 days')[0];
        $count = $verify["count"];
        $this->logger->log("Processing payments for " . $count . " TOTAL trips\n");
        $limit = 200;
        $lastId = null;
        while ($count > 0){
            $verify = $this->tripPaymentsService->getTripPaymentsForPaymentDetails('180 days', $lastId, $limit)[0];
            if ($verify["count"] == 0) {
                break;
            }
            $tripPayments = $this->tripPaymentsService->getTripPaymentsForPayment(null, '-180 days', $lastId, $limit);
            $lastId = $verify["last"];
            $count = $verify["count"];
            $this->logger->log("Processing payments for " . count($tripPayments) . " trips\n");
            $this->processPaymentsService->processPayments(
                $tripPayments,
                $this->avoidEmails,
                $this->avoidCartasi,
                $this->avoidPersistance
            );
            // clear the entity manager cache
            $this->entityManager->clear();
        }

        $this->processPaymentsService->processPaymentsCompleted($this->avoidEmails);

        $this->logger->log("Done processing payments\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    private function reProcessWrongPayments()
    {
        $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;reProcessWrongPayments;start\n");
        $tripPaymentsWrong = $this->tripPaymentsService->getTripPaymentsWrong(null, '-48 hours');  //TODO only dev put -2 days
        $this->logger->log(date_create()->format('H:i:s').";INF;reProcessWrongPayments;count(tripPaymentsWrong);" . count($tripPaymentsWrong) . "\n");

        $this->processPaymentsService->processPayments(
            $tripPaymentsWrong,
            $this->avoidEmails,
            $this->avoidCartasi,
            $this->avoidPersistance
        );

        $this->processPaymentsService->processCustomersDisabledAfterReProcess(
            $tripPaymentsWrong,
            $this->avoidEmails,
            $this->avoidCartasi,
            $this->avoidPersistance
        );

        $this->logger->log(date_create()->format('H:i:s').";INF;reProcessWrongPayments;end\n");
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

    /*
     * Re try to pay trips marked as "wrong payments" in a defined period of time
     */
    public function retryWrongPaymentsTimeAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $start = $request->getParam('startTimestamp');
        $end = $request->getParam('endTimestamp');
        $this->avoidEmails = true;  // force avoid send email during re try wong paiment
        $this->avoidCartasi = $request->getParam('no-cartasi') || $request->getParam('c');
        $this->avoidPersistance = $request->getParam('no-db') || $request->getParam('d');

        if (is_null($start) && is_null($end)){
            $this->logger->log(date_create()->format('y-m-d H:i:s') . ";ERR;retryWrongPaymentsTimeAction;Error Retry: missing time parameters\n");
            exit();
        }

        if (!$this->paymentScriptRunsService->isRunning()) {
            $scriptId = $this->paymentScriptRunsService->scriptStarted();

            $this->reProcessWrongTimePayments($start, $end);

            $this->paymentScriptRunsService->scriptEnded($scriptId);
            $this->entityManager->clear();

        } else {
            $this->logger->log(date_create()->format('y-m-d H:i:s') . ";ERR;retryWrongPaymentsTimeAction;Error Retry: Pay invoice is running\n");
        }
    }

    private function reProcessWrongTimePayments($start, $end)
    {
        $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;reProcessWrongPaymentsTime;start\n");
        $verify = $this->tripPaymentsService->getWrongTripPaymentsDetails($start, $end)[0];
        $count = $verify["count"];
        $this->logger->log(date_create()->format('H:i:s').";INF;reProcessWrongPaymentsTime;count(tripPaymentsWrong);" . $count . "\n");
        $limit = 200;
        $lastId = null;
        while ($count > 0){
            $verify = $this->tripPaymentsService->getWrongTripPaymentsDetails($start, $end, $lastId, $limit)[0];
            if ($verify["count"] == 0) {
                break;
            }
            $tripPaymentsWrong = $this->tripPaymentsService->getTripPaymentsWrongTime(null, $start, $end, $lastId, $limit);
            $lastId = $verify["last"];
            $count = $verify["count"];

            $this->logger->log(date_create()->format('H:i:s').";INF;reProcessWrongPaymentsTime;count(tripPaymentsWrong);" . count($tripPaymentsWrong) . "\n");
            $this->processPaymentsService->processPayments(
                $tripPaymentsWrong,
                $this->avoidEmails,
                $this->avoidCartasi,
                $this->avoidPersistance
            );

            $this->processPaymentsService->processCustomersDisabledAfterReProcess(
                $tripPaymentsWrong,
                $this->avoidEmails,
                $this->avoidCartasi,
                $this->avoidPersistance
            );
            // clear the entity manager cache
            $this->entityManager->clear();

        }
        $this->logger->log(date_create()->format('H:i:s').";INF;reProcessWrongPayments;end\n");
    }
}
