<?php

namespace Application\Controller;

use SharengoCore\Entity\Queries\FirstPaymentInvoicesVersionTwo;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;

class FixInvoicesBodyController extends AbstractActionController
{
    /**
     * @var FirstPaymentInvoicesVersionTwo
     */
    private $invoicesQuery;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param FirstPaymentInvoicesVersionTwo $invoicesQuery
     * @param EntityManager $entityManager
     */
    public function __construct(
        FirstPaymentInvoicesVersionTwo $invoicesQuery,
        EntityManager $entityManager
    ) {
        $this->invoicesQuery = $invoicesQuery;
        $this->entityManager = $entityManager;
    }

    public function fixInvoicesBodyAction()
    {
        // I put everything in the controller since this should be used only once

        $invoices = $this->invoicesQuery->__invoke();

        foreach ($invoices as $invoice) {
            $body = $invoice->getContent()['body'];

            $contentsBody = $body['contents']['body'];

            foreach ($contentsBody as &$line) {

                foreach ($line as &$field) {
                    if (!is_array($field)) {
                        $field = [$field];
                    }
                }
            }

            $body['contents']['body'] = $contentsBody;

            $invoice->setContentBody($body);

            $this->entityManager->persist($invoice);
        }

        $this->entityManager->flush();
    }
}
