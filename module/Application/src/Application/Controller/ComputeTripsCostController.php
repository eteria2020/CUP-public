<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\InvoicesService;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;

class ComputeTripsCostController extends AbstractActionController
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var boolean defines verbosity
     */
    private $verbose;

    /**
     * @param TripsService $tripsService
     * @param TripCostService $tripCostService
     * @param TripPaymentsService $tripPaymentsService
     * @param InvoicesService $invoicesService
     * @param EntityManager $entityManager
     */
    public function __construct(
        TripsService $tripsService,
        TripCostService $tripCostService,
        TripPaymentsService $tripPaymentsService,
        InvoicesService $invoicesService,
        EntityManager $entityManager
    ) {
        $this->tripsService = $tripsService;
        $this->tripCostService = $tripCostService;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->invoicesService = $invoicesService;
        $this->entityManager = $entityManager;
    }

    public function computeTripsCostAction()
    {
        $tripsToBeProcessed = $this->tripsService->getTripsForCostComputation();

        foreach ($tripsToBeProcessed as $trip) {
            echo "processing trip ".$trip->getId()."\n";
            $this->tripCostService->computeTripCost($trip);
        }

        echo "\nDONE\n";
    }

    public function invoiceTripsAction()
    {
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $this->verbose = $request->getParam('verbose') || $request->getParam('v');
        $this->writeToConsole("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
        $invoicesCreated = 0;

        // get all trip_payments without invoice
        $tripPayments = $this->tripPaymentsService->getTripPaymentsNoInvoice();
        $this->writeToConsole('Retrieved ' . count($tripPayments) . " tripPayments\n");

        if (count($tripPayments) != 0) {
            $this->writeToConsole("\nFound tripPayments on date:\n");
        }

        // group by date and customer
        $orderedTripPayments = [];
        foreach ($tripPayments as $tripPayment) {
            // retrieve date and customerId from tripPayment
            $date = $tripPayment->getPayedSuccessfullyAt()->format('Y-m-d');
            $customerId = $tripPayment->getTrip()->getCustomer()->getId();
            // if first tripPayment for that day, create the entry
            if (isset($orderedTripPayments[$date])) {
                // if first tripPayment for that customer, create the entry
                if (!isset($orderedTripPayments[$date][$customerId])) {
                    $orderedTripPayments[$date][$customerId] = [];
                }
            } else {
                $orderedTripPayments[$date] = [$customerId => []];
                $this->writeToConsole($date . "\n");
            }
            // add the tripPayment in the correct group
            array_push($orderedTripPayments[$date][$customerId], $tripPayment);
        }
        $this->writeToConsole("\n");

        // loop through each day
        foreach ($orderedTripPayments as $dateKey => $tripPaymentsForDate) {
            // generate date for invoices
            $date = date_create_from_format('Y-m-d', $dateKey);
            $this->writeToConsole("Generating invoices for date: " . $dateKey . "\n\n");
            // loop through each customer in day
            foreach ($$tripPaymentsForDate as $customerId => $tripPaymentsForCustomer) {
                $this->writeToConsole("Generating invoice for customer: " . $customerId . "\n");
                // get customer for group of tripPayments
                $customer = $tripPaymentsForCustomer[0]->getTrip()->getCustomer();
                // generate invoice from group of tripPayments
                $invoice = $this->invoicesService->prepareInvoiceForTrips($customer, $tripPaymentsForCustomer);
                $this->writeToConsole("Invoice created: " . $invoice->getId() . "\n");
                $this->entityManager->persist($invoice);
                $this->writeToConsole("EntityManager: invoice persisted\n\n");
                $invoicesCreated ++;
            }
        }

        // save invoices to db
        if (!$dryRun) {
            $this->writeToConsole("EntityManager: about to flush\n");
            $this->entityManager->flush();
            $this->writeToConsole("EntityManager: flushed\n");
        }

        $this->writeToConsole("Created " . $invoicesCreated . " invoices\n\n");
        $this->writeToConsole("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    private function writeToConsole($string)
    {
        if ($this->verbose) {
            fwrite(STDOUT, $string);
        }
    }
}
