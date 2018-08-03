<?php

namespace Application\Controller;

use SharengoCore\Entity\Trips;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Entity\Customers;
use SharengoCore\Service\TripsService;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use \Cartasi\Entity\Repository\TransactionsRepository;

class ConsolePaymentsController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

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
     * @var TripsService
     */
    private $tripsService;


    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var TransactionsRepository
     */
    private $cartasiTransactionsRepository;


    /**
     * @param EntityManager $entityManager
     * @param TripPaymentsService $tripPaymentsService
     * @param PaymentsService $paymentsService
     * @param CustomersService $customersService
     * @param TripsService $tripsService
     * @param Logger $logger
     * @param TransactionsRepository
     */
    public function __construct(
        EntityManager $entityManager,
        TripPaymentsService $tripPaymentsService,
        PaymentsService $paymentsService,
        CustomersService $customersService,
        TripsService $tripsService,
        Logger $logger,
        TransactionsRepository $cartasiTransactionsRepository
    ) {
        $this->entityManager = $entityManager;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->paymentsService = $paymentsService;
        $this->customersService = $customersService;
        $this->tripsService = $tripsService;
        $this->logger = $logger;
        $this->cartasiTransactionsRepository = $cartasiTransactionsRepository;
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

    public function preauthorizationAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $avoidEmails = $request->getParam('no-emails') || $request->getParam('e');
        $avoidCartasi = $request->getParam('no-cartasi') || $request->getParam('c');
        $avoidPersistance = $request->getParam('no-db') || $request->getParam('d');

        $customerId = $request->getParam('customerId');

        $tripId = $request->getParam('tripId');
        $customer = $this->customersService->findById($customerId);
        $trip = $this->tripsService->getTripById($tripId);

        if(!($customer instanceof Customers) || !($trip instanceof Trips)){
            $log = json_encode(['response'=>-21,'customer_id'=>$customerId, 'trip_id'=>$tripId, 'date' => date_create()->format('Y-m-d H:i:s')]);
            $this->logger->log("\n" . $log);
            exit();
        }

        try {
            $response = $this->paymentsService->tryPreAuthorization(
                $customer,
                $trip,
                $avoidEmails,
                $avoidCartasi,
                $avoidPersistance
            );
        } catch (\Exception $e){
            $log = json_encode(['response'=>-21,'customer_id'=>$customerId, 'trip_id'=>$tripId, 'date' => date_create()->format('Y-m-d H:i:s')]);
            $this->logger->log("\n" . $log);
            exit();
        }
        //TODO: aggiornare l'error code nei vari casi - nb su error code deve essere numero positivo
        $this->entityManager->beginTransaction();
        $this->entityManager->persist($trip->setErrorCode($response));
        $this->entityManager->flush();
        $this->entityManager->commit();

        $log = json_encode(['response'=>-$response,'customer_id'=>$customerId, 'trip_id'=>$tripId, 'date' => date_create()->format('Y-m-d H:i:s')]);
        $this->logger->log("\n" . $log);
    }

    public function refundAction() {
        $request = $this->getRequest();
        //$customerId = $request->getParam('customer'); // not use
        $transactionId = $request->getParam('transaction'); //codTrans
        //$amount = $request->getParam('amount');

        $transactionId = explode("-", $transactionId);
        $customerId = $transactionId[1];
        $transactionId = $transactionId[0];

        $customer = $this->customersService->findById($customerId);
        $transaction = $this->cartasiTransactionsRepository->findOneById($transactionId);

        if (is_null($transaction)) {
            echo "no transaction ('.$transactionId.') found\n";
            exit();
        }else if (is_null($customer)){
            echo "no customer ('.$customerId.') found\n";
            exit();
        } else {
            $amount = $transaction->getAmount();
            $this->paymentsService->refund($transactionId, $customer, $amount);
            echo "finished ".$transactionId . "\n";
        }
    }


}
