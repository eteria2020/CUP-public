<?php

namespace Application\Controller;

use SharengoCore\Entity\CustomersBonus;
use SharengoCore\Service\BonusService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;

class BirthdayBonusController extends AbstractActionController
{
    /**
     * @var BonusService
     */
    private $bonusService;

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var mixed[]
     */
    private $config;

    /**
     * @var bool
     */
    private $dryRun;

    /**
     * @param BonusService $bonusService
     * @param CustomersService $customersService
     * @param EntityManager $entityManager
     * @param Logger $logger
     * @param mixed[] $config
     */
    public function __construct(
        BonusService $bonusService,
        CustomersService $customersService,
        EntityManager $entityManager,
        Logger $logger,
        array $config
    ) {
        $this->bonusService = $bonusService;
        $this->customersService = $customersService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->config = $config;
    }

    public function assignBirthdayBonusesAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $this->dryRun = $request->getParam('dry-run') || $request->getParam('d');

        $this->logger->log("\nStarted assigning birthday bonuses\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // Get all customers
        $this->logger->log("Acquiring customers...");
        $customers = $this->customersService->getAllForBirthdayBonusAssignement();
        $this->logger->log("got " . count($customers) . " customers\n");

        if (count($customers) > 0) {

            // Prepare dates
            $date = date_create('tomorrow')->format('Y-m-d');
            $from = $date . ' 00:00:00';
            $to = $date . ' 23:59:59';
            $this->logger->log("Bonuses will be valid from: " . $from . " to: " . $to . "\n\n");

            // Generate CustomersBonuses
            foreach ($customers as $customer) {
                $bonus = CustomersBonus::createBonus(
                    $customer,
                    $this->config['total'],
                    $this->config['description'],
                    $to,
                    $from,
                    'birthday'
                );
                $this->logger->log("Generated bonus for customer: " . $customer->getId() . "\n");

                $this->entityManager->persist($bonus);
            }

            // Flush EntityManager
            if (!$this->dryRun) {
                $this->logger->log("\nFlushing entity manager...");
                $this->entityManager->flush();
                $this->logger->log("done\n");
            }

        } else {
            $this->logger->log("No customers to process\n");
        }

        $this->logger->log("\nDone\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
