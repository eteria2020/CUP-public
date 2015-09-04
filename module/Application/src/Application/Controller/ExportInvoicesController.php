<?php

namespace Application\Controller;

use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\TripsService;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;

class ExportInvoicesController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        EntityManager $entityManager,
        CustomersService $customersService,
        TripsService $tripsService,
        Logger $logger
    ) {
        $this->entityManager = $entityManager;
        $this->customersService = $customersService;
        $this->tripsService = $tripsService;
        $this->logger = $logger;
    }

    public function exportInvoicesAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // Generate customers registries
        $customers = $this->customersService->getCustomersForExport();

        foreach ($customers as $customer) {
            //$this->logger->log("Exporting customer: " . $customer->getId() . "\n");
            $vat = $customer->getVat();

            $record = "GEN;" . // 10
                $customer->getId() . ";" . // 41
                $customer->getCard()->getCode() . ";" . //50
                $vat . ";" . // 60
                ($vat != null ? 1 : 0) . ";" . // 61
                ($vat != null ? 0 : 1) . ";" . // 358
                $customer->getTaxCode() . ";" . // 70
                ($vat != null ? 2 : 3) . ";" . // 80
                ";" . // 90
                ";" . // 95
                $customer->getAddress() . ";" . // 100
                ";" . // 105
                $customer->getPhone() . ";" . // 160
                $customer->getMobile() . ";" . // 170
                $customer->getZipCode() . ";" . // 110
                $customer->getTown() . ";" . // 120
                $customer->getProvince() . ";" . // 130
                $customer->getCountry() . ";" . // 140
                $customer->getSurname() . ";" . // 230
                $customer->getName() . ";" . // 231
                $customer->getBirthTown() . ";" . // 232
                $customer->getBirthProvince() . ";" . // 233
                $customer->getBirthDate()->format("d/m/Y") . ";" . // 234
                $customer->getGender() . ";" . // 235
                $customer->getBirthCountry() . ";" . // 236
                "C01;" . // 240
                "200;" . // 330
                "CC001;"; // 581

            $this->logger->log($record . "\n\n");
        }

        if (!$dryRun) {
            $this->logger->log("EntityManager: flushing\n\n");
            $this->entityManager->flush();
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
