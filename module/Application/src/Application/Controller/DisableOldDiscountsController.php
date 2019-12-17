<?php

namespace Application\Controller;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\OldCustomerDiscountsService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class DisableOldDiscountsController extends AbstractActionController
{
    /**
     * @var CustomersService $customersService
     */
    private $customersService;

    /**
     * @var OldCustomerDiscountsService $oldCustomerDiscountsService
     */
    private $oldCustomerDiscountsService;

    /**
     * @var Logger $logger
     */
    private $logger;

    public function __construct(
        CustomersService $customersService,
        OldCustomerDiscountsService $oldCustomerDiscountsService,
        Logger $logger
    ) {
        $this->customersService = $customersService;
        $this->oldCustomerDiscountsService = $oldCustomerDiscountsService;
        $this->logger = $logger;

        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);
    }

    /**
     * Disabled the discount of the customer, and send a email.
     *
     * N.B. Send mail is disabled.
     */
    public function disableOldDiscountsAction()
    {

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $noEmail = $request->getparam('no-email') || $request->getParam('e');

        $customersOneYearOld = $this->customersService->retrieveOneYearOldCustomers();
        $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;disableOldDiscountsAction;count=" . count($customersOneYearOld) . ";run=" . $dryRun . ";noEmail=" . $noEmail."\n");

        foreach ($customersOneYearOld as $customer) {
            $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;disableOldDiscountsAction;" . $customer->getId() . ";" . $customer->getEmail() . "\n");
            $this->oldCustomerDiscountsService->disableCustomerDiscount($customer, !$dryRun, !$noEmail);
        }

        $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;disableOldDiscountsAction;end\n");
    }

    /**
     *
     * One week bebore the discoun expired, sen an email to notify the customer.
     *
     * N.B. deprecated: batch disabled in crontab
     */
    public function notifyDisableDiscountAction()
    {
        $this->logger->log(
            "\nStarted notifying customers that soon their discount will be disabled\n" .
            "time = " . date_create()->format('Y-m-d H:i:s') . "\n\n"
        );

        $request = $this->getRequest();
        $noEmail = $request->getparam('no-email') || $request->getParam('e');

        $customersToNotify = array_filter(
            $this->customersService->retrieveCustomersWithDiscountOldInAWeek(),
            function (Customers $customer) {
                return $customer->getFirstPaymentCompleted();
            }
        );

        $this->logger->log("Notifying " . count($customersToNotify) . " customers\n");

        foreach ($customersToNotify as $customer) {
            $this->logger->log(
                "Notifying customer " . $customer->getId() .
                " - " . $customer->getEmail() . "\n"
            );

            if (!$noEmail) {
                $this->oldCustomerDiscountsService->notifyCustomer($customer);
            }
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    /**
     * Renew the old customer's discounts.
     */
    public function renewOldDiscountsAction() {
        $newDiscount = 15;

        $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;renewOldDiscountsAction;start;" . $newDiscount."\n");
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $noEmail = $request->getparam('no-email') || $request->getParam('e');

        $customersOneYearOld = $this->customersService->retrieveOneYearOldCustomers();
        foreach ($customersOneYearOld as $customer) {
            $this->logger->log(
                date_create()->format('y-m-d H:i:s') .
                ";INF;renewOldDiscountsAction;" . $customer->getId() .
                ";" . $customer->getEmail() . "\n"
            );
            $this->oldCustomerDiscountsService->renewCustomerDiscount($customer, !$dryRun, !$noEmail, $newDiscount);
        }
        $this->logger->log(date_create()->format('y-m-d H:i:s') . ";INF;renewOldDiscountsAction;end\n");
    }
}
