<?php

namespace Application\Controller;

use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class ConsolePaymentsController extends AbstractActionController
{
    /**
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @var CustomerService
     */
    private $customersService;

    /**
     * @var PaymentsService
     */
    private $paymentsService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param TripPaymentsService $tripPaymentsService
     * @param PaymentsService $paymentsService
     * @param CustomersService $customersService
     * @param Logger $logger
     */
    public function __construct(
        TripPaymentsService $tripPaymentsService,
        PaymentsService $paymentsService,
        CustomersService $customersService,
        Logger $logger
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->paymentsService = $paymentsService;
        $this->customersService = $customersService;
        $this->logger = $logger;
    }

    public function makeUserPayAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $avoidEmails = $request->getParam('no-emails') || $request->getParam('e');
        $avoidCartasi = $request->getParam('no-cartasi') || $request->getParam('c');
        $avoidPersistance = $request->getParam('no-db') || $request->getParam('d');

        $customerId = $this->getRequest()->getParam('customerId');
        $customer = $this->customersService->findById($customerId);

        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $tripsPayments = $this->tripPaymentsService->getTripPaymentsForUserPayment($customer);
        $this->logger->log("Trips found: " . count($tripsPayments) . "\n");

        foreach ($tripsPayments as $tripPayment) {
            $this->logger->log("Processing trip payment " . $tripPayment->getId() . "\n");
            $this->paymentsService->tryPayment(
                $tripPayment,
                $avoidEmails,
                $avoidCartasi,
                $avoidPersistance
            );
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
