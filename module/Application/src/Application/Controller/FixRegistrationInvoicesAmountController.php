<?php

namespace Application\Controller;

use SharengoCore\Entity\Queries\WrongAmountRegistrationInvoices;
use SharengoCore\Entity\Invoices;
use SharengoCore\Service\invoicesService;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;

class FixRegistrationInvoicesAmountController extends AbstractActionController
{
    private $entityManager;
    private $invoicesQuery;
    private $invoicesService;

    public function __construct(
        EntityManager $entityManager,
        WrongAmountRegistrationInvoices $invoicesQuery,
        InvoicesService $invoicesService
    ) {
        $this->entityManager = $entityManager;
        $this->invoicesQuery = $invoicesQuery;
        $this->invoicesService = $invoicesService;
    }

    public function fixRegistrationInvoicesAmountAction()
    {
        // THIS SHOULD BE USED ONLY THIS ONCE!!! DO NOT REUSE!!

        $invoicesWithWrongAmount = $this->invoicesQuery->__invoke();

        echo 'count: ' . count($invoicesWithWrongAmount);

        foreach ($invoicesWithWrongAmount as $invoice) {
            $rightAmount = $invoice['amount'];
            $invoice = $invoice['invoice'];

            $rightAmounts = [
                'sum' => $this->invoicesService->calculateAmountsWithTaxesFromTotal($rightAmount),
                'iva' => $invoice->getIva()
            ];

            $newInvoice = Invoices::createInvoiceForFirstPayment(
                $invoice->getCustomer(),
                $invoice->getVersion(),
                $rightAmounts,
                $invoice->getInvoiceDate()
            );

            // evil tricks to edit an immutable object
            $reflectionInvoice = new \ReflectionObject($invoice);

            $invoiceAmount = $reflectionInvoice->getProperty('amount');
            $invoiceAmount->setAccessible(true);
            $invoiceAmount->setValue($invoice, $newInvoice->getAmount());

            $invoiceContent = $reflectionInvoice->getProperty('content');
            $invoiceContent->setAccessible(true);
            $invoiceContent->setvalue($invoice, $newInvoice->getContent());

            $this->entityManager->persist($invoice);
        }

        $this->entityManager->flush();

        echo 'done';
    }
}
