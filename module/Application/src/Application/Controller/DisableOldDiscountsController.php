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

    public function disableOldDiscountsAction()
    {
        $this->logger->log(
            "\nStarted disabling old customers discounts\n" .
            "time = " . date_create()->format('Y-m-d H:i:s') . "\n\n"
        );

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $noEmail = $request->getparam('no-email') || $request->getParam('e');

        $customersOneYearOld = $this->customersService->retrieveOneYearOldCustomers();

        $this->logger->log("Disabling " . count($customersOneYearOld) . " customers discounts\n");

        foreach ($customersOneYearOld as $customer) {
            $this->logger->log(
                "Disabling discount for customer " . $customer->getId() .
                " - " . $customer->getEmail() . "\n"
            );
            $this->oldCustomerDiscountsService->disableCustomerDiscount($customer, !$dryRun, !$noEmail);
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

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
}
