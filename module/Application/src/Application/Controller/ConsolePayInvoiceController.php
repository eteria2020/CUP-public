<?php

namespace Application\Controller;

use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\ExtraPaymentsService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Service\ProcessPaymentsService;
use SharengoCore\Service\ProcessExtraService;
use SharengoCore\Service\PaymentScriptRunsService;
use SharengoCore\Service\ExtraScriptRunsService;
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
     * @var ExtraPaymentsService
     */
    private $extraPaymentsService;

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
     * @var ExtraScriptRunsService
     */
    private $extraScriptRunsService;
    
    /**
     * @var ProcessExtraService
     */
    private $processExtraService;

    /**
     * @param TripPaymentsService $tripPaymentsService
     * @param ExtraPaymentsService $extraPaymentsService
     * @param InvoicesService $invoicesService
     * @param Logger $logger
     * @param ProcessPaymentsService $processPaymentsService
     * @param ProcessExtraService $processExtraService
     * @param EntityManager $entityManager
     * @param PaymentScriptRunsService $paymentScriptRunsService
     * @param ExtraScriptRunsService $extraScriptRunsService
     */
    public function __construct(
        TripPaymentsService $tripPaymentsService,
        ExtraPaymentsService $extraPaymentsService,
        InvoicesService $invoicesService,
        Logger $logger,
        ProcessPaymentsService $processPaymentsService,
        ProcessExtraService $processExtraService,
        EntityManager $entityManager,
        PaymentScriptRunsService $paymentScriptRunsService,
        ExtraScriptRunsService $extraScriptRunsService
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->extraPaymentsService = $extraPaymentsService;
        $this->invoicesService = $invoicesService;
        $this->logger = $logger;
        $this->processPaymentsService = $processPaymentsService;
        $this->processPaymentsService->setLogger($this->logger);
        $this->processExtraService = $processExtraService;
        $this->processExtraService->setLogger($this->logger);
        $this->entityManager = $entityManager;
        $this->paymentScriptRunsService = $paymentScriptRunsService;
        $this->extraScriptRunsService = $extraScriptRunsService;
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
        $now = date_create();
        $timestampEndParam = '-48 hours';
        if ($now >= date_create('18:59:00') && $now <= date_create('19:10:00')){
            $timestampEndParam = '-60 days';
        }
        $tripPaymentsWrong = $this->tripPaymentsService->getTripPaymentsWrong(null, $timestampEndParam);  //TODO only dev put -2 days
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
            $this->avoidPersistance,
            '-8 days'
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

        if ($start == '' && $end == ''){
            $now = date_create();
            if ($now >= date_create('17:59:00') && $now <= date_create('19:10:00')){
                $start = date_create('-60 days');
                $start = $start->format('Y-m-d H:i:s');
                $end = $now->format('Y-m-d H:i:s');
            } else {
                $this->logger->log(date_create()->format('y-m-d H:i:s') . ";ERR;retryWrongPaymentsTimeAction;Error Retry: missing time parameters\n");
                exit();
            }
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
        $limit = 100;
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
    
    /*
     * Re try to pay extra marked as "wrong payments" in last -2 days
     */
    public function retryWrongExtraAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);
        
        $request = $this->getRequest();
        //$this->avoidEmails = $request->getParam('no-emails') || $request->getParam('e');
        $this->avoidEmails = true;  // force avoid send email during re try wong paiment
        $this->avoidCartasi = $request->getParam('no-cartasi') || $request->getParam('c');
        $this->avoidPersistance = $request->getParam('no-db') || $request->getParam('d');   

        if (!$this->extraScriptRunsService->isRunning()) {
            $scriptId = $this->extraScriptRunsService->scriptStarted();

            $this->reProcessWrongExtra();

            $this->extraScriptRunsService->scriptEnded($scriptId);
            $this->entityManager->clear();

            //$this->generateInvoices();
        } else {
            $this->logger->log(date_create()->format('y-m-d H:i:s') . ";ERR;retryWrongExtrasAction;Error Retry: Pay invoice is running\n");
        }
    }
    
    private function reProcessWrongExtra()
    {
        $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;reProcessWrongExtra;start\n");
        $now = date_create();
        $timestampEndParam = '-48 hours';
        if ($now >= date_create('18:59:00') && $now <= date_create('19:10:00')){
            $timestampEndParam = '-60 days';
        }
        $extraPaymentsWrong = $this->extraPaymentsService->getExtraPaymentsWrong(null, $timestampEndParam);  //TODO only dev put -2 days
        $this->logger->log(date_create()->format('H:i:s').";INF;reProcessWrongExtra;count(extraPaymentsWrong);" . count($extraPaymentsWrong) . "\n");

        $this->processExtraService->processPayments(
            $extraPaymentsWrong,
            $this->avoidEmails,
            $this->avoidCartasi,
            $this->avoidPersistance
        );

        $this->processExtraService->processCustomersDisabledAfterReProcess(
            $extraPaymentsWrong,
            $this->avoidEmails,
            $this->avoidCartasi,
            $this->avoidPersistance,
            '-8 days'
        );

        $this->logger->log(date_create()->format('H:i:s').";INF;reProcessWrongExtra;end\n");
    }
    
    /*
     * Re try to pay extra marked as "wrong payments" in a defined period of time
     */
    public function retryWrongExtraTimeAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $start = $request->getParam('startTimestamp');
        $end = $request->getParam('endTimestamp');
        $this->avoidEmails = true;  // force avoid send email during re try wong paiment
        $this->avoidCartasi = $request->getParam('no-cartasi') || $request->getParam('c');
        $this->avoidPersistance = $request->getParam('no-db') || $request->getParam('d');

        if ($start == '' && $end == '') {
            $now = date_create();
            $start = date_create('-60 days');
            $start = $start->format('Y-m-d H:i:s');
            $end = $now->format('Y-m-d H:i:s');
        }

        if (!$this->extraScriptRunsService->isRunning()) {
            $scriptId = $this->extraScriptRunsService->scriptStarted();

            $this->reProcessWrongTimeExtra($start, $end);

            $this->extraScriptRunsService->scriptEnded($scriptId);
            $this->entityManager->clear();

        } else {
            $this->logger->log(date_create()->format('y-m-d H:i:s') . ";ERR;retryWrongExtraTimeAction;Error Retry: Pay invoice is running\n");
        }
    }
    
     private function reProcessWrongTimeExtra($start, $end)
    {
        $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;reProcessWrongTimeExtra;start\n");
        $verify = $this->extraPaymentsService->getWrongExtraPaymentsDetails($start, $end)[0];
        $count = $verify["count"];
        $this->logger->log(date_create()->format('H:i:s').";INF;reProcessWrongTimeExtra;count(extraPaymentsWrong);" . $count . "\n");
        $limit = 100;
        $lastId = null;
        while ($count > 0){
            $verify = $this->extraPaymentsService->getWrongExtraPaymentsDetails($start, $end, $lastId, $limit)[0];
            if ($verify["count"] == 0) {
                break;
            }
            $extraPaymentsWrong = $this->extraPaymentsService->getExtraPaymentsWrongTime(null, $start, $end, $lastId, $limit);
            $lastId = $verify["last"];
            $count = $verify["count"];

            $this->logger->log(date_create()->format('H:i:s').";INF;reProcessWrongTimeExtra;count(extraPaymentsWrong);" . count($extraPaymentsWrong) . "\n");
            $this->processExtraService->processPayments(
                $extraPaymentsWrong,
                $this->avoidEmails,
                $this->avoidCartasi,
                $this->avoidPersistance
            );

            $this->processExtraService->processCustomersDisabledAfterReProcess(
                $extraPaymentsWrong,
                $this->avoidEmails,
                $this->avoidCartasi,
                $this->avoidPersistance
            );
            // clear the entity manager cache
            $this->entityManager->clear();

        }
        $this->logger->log(date_create()->format('H:i:s').";INF;reProcessWrongTimeExtra;end\n");
    }
    
    public function payInvoiceExtraAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $this->avoidEmails = $request->getParam('no-emails') || $request->getParam('e');
        $this->avoidCartasi = $request->getParam('no-cartasi') || $request->getParam('c');
        $this->avoidPersistance = $request->getParam('no-db') || $request->getParam('d');

        if (!$this->extraScriptRunsService->isRunning()) {
            $scriptId = $this->extraScriptRunsService->scriptStarted();
            $this->processExtra();

            $this->extraScriptRunsService->scriptEnded($scriptId);

            // clear the entity manager cache
            $this->entityManager->clear();

            //$this->generateInvoices();
        } else {
            $this->logger->log("\nError: Pay invoice Extra is running\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
        }
    }
    
    private function processExtra()
    {
        $this->logger->log("\nStarted processing extra\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $verify = $this->extraPaymentsService->getExtraPaymentsForPaymentDetails('180 days')[0];
        $count = $verify["count"];
        $this->logger->log("Processing extra for " . $count . " TOTAL extra\n");
        $limit = 200;
        $lastId = null;
        while ($count > 0){
            $verify = $this->extraPaymentsService->getExtraPaymentsForPaymentDetails('180 days', $lastId, $limit)[0];
            if ($verify["count"] == 0) {
                break;
            }
            $extraPayments = $this->extraPaymentsService->getExtraPaymentsForPayment(null, '-180 days', $lastId, $limit);
            $lastId = $verify["last"];
            $count = $verify["count"];
            $this->logger->log("Processing payments for " . count($extraPayments) . " extra\n");
            $this->processExtraService->processPayments(
                $extraPayments,
                $this->avoidEmails,
                $this->avoidCartasi,
                $this->avoidPersistance
            );
            // clear the entity manager cache
            $this->entityManager->clear();
        }

        $this->processExtraService->processPaymentsCompleted($this->avoidEmails);

        $this->logger->log("Done processing extra\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
