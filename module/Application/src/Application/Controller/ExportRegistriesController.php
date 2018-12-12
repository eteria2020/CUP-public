<?php

namespace Application\Controller;

use SharengoCore\Service\SimpleLoggerService as Logger;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\FleetService;
use SharengoCore\Service\EmailService;
use SharengoCore\Entity\Invoices;
use SharengoCore\Exception\FleetNotFoundException;

use Zend\Mvc\Controller\AbstractActionController;

class ExportRegistriesController extends AbstractActionController
{
    const TYPE_INVOICES = "Invoices";

    const TYPE_CUSTOMERS = "Customers";

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var FleetService
     */
    private $fleetService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $exportConfig;

    /**
     * @var array
     */
    private $alertConfig;

    /**
     * Specifies wether files should be written
     * @var boolean
     */
    private $dryRun;

    /**
     * Specifies wether data for customers will be exported
     * @var boolean
     */
    private $noCustomers;

    /**
     * Specifies wether data for invoices will be exported
     * @var boolean
     */
    private $noInvoices;

    /**
     * Specifies wether data for all days will be exported
     * @var boolean
     */
    private $all;

    /**
     * Specifies wether ftp connection and upload will be made
     * @var boolean
     */
    private $noFtp;

    /**
     * Specifies prepended text to filenames.
     * @var string
     */
    private $testName;

    /**
     * Connection to ftp server
     * @var resource | null
     */
    private $ftpConn = null;

    /**
     * @param CustomersService $customersService
     * @param InvoicesService $invoicesService
     * @param FleetService $fleetService
     * @param EmailService $emailService
     * @param Logger $logger
     * @param array $exportConfig
     * @param array $alertConfig
     */
    public function __construct(
        CustomersService $customersService,
        InvoicesService $invoicesService,
        FleetService $fleetService,
        EmailService $emailService,
        Logger $logger,
        $exportConfig,
        $alertConfig
    ) {
        $this->customersService = $customersService;
        $this->invoicesService = $invoicesService;
        $this->fleetService = $fleetService;
        $this->emailService = $emailService;
        $this->logger = $logger;
        $this->exportConfig = $exportConfig;
        $this->alertConfig = $alertConfig;
    }

    /**
     * Available params are:
     *     -d (does not generate files)
     *     -c (does not export customers data)
     *     -i (does not export invoices data)
     *     -a (exports data for all days, overrides --date)
     *     -f (does not connect to ftp)
     *     -t (appends "test-" to filenames)
     *     --date= (export for specified date, date_create formats accepted)
     */
    public function exportRegistriesAction()
    {
        // Setup logger
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        // Get/Set params
        $request = $this->getRequest();
        $this->dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $this->noCustomers = $request->getParam('no-customers') || $request->getParam('c');
        $this->noInvoices = $request->getParam('no-invoices') || $request->getParam('i');
        $this->all = $request->getParam('all') || $request->getParam('a');
        $this->noFtp = $request->getParam('no-ftp') || $request->getParam('f');
        $this->testName = $request->getParam('test-name') || $request->getParam('t') ? 'test-' : '';
        $path = $this->exportConfig['path'];

        $this->logger->log(sprintf("%s;INF;exportRegistriesAction;start;%s;%s;%s;%s;%s;%s;%s\n",
            date_create()->format('ymd His'),
            $this->dryRun,
            $this->noCustomers,
            $this->noInvoices,
            $this->all,
            $this->noFtp,
            $this->testName,
            $path));

        // Retrieve invoices
        $invoicesByDate = $this->retrieveData();

        // Start the ftp connection and login
        $this->connectToServer($this->exportConfig);

        foreach ($invoicesByDate as $invoices) {
            $date = $invoices[0]->getDateTimeDate();
            $this->logger->log(sprintf("%s;INF;exportRegistriesAction;date;%s\n",
                date_create()->format('ymd His'),
                $date->format('Y-m-d')));

            $invoicesEntries = [];
            $customersEntries = [];

            // Generate the data to be exported
            foreach ($invoices as $invoice) {
                $fleetName = $invoice->getFleet()->getName();
                if (!$this->noInvoices) {

                    $this->logger->log(sprintf("%s;INF;exportRegistriesAction;invoice;%d\n",
                        date_create()->format('ymd His'),
                        $invoice->getId()));

                    if (!array_key_exists($fleetName, $invoicesEntries)) {
                        $invoicesEntries[$fleetName] = '';
                    }
                    $invoicesEntries[$fleetName] .= $this->invoicesService->getExportDataForInvoice($invoice) . "\r\n";
                }

                if (!$this->noCustomers) {
                    $this->logger->log(sprintf("%s;INF;exportRegistriesAction;customer;%d\n",
                        date_create()->format('ymd His'),
                        $invoice->getCustomer()->getId()));

                    if (!array_key_exists($fleetName, $customersEntries)) {
                        $customersEntries[$fleetName] = '';
                    }
                    $customersEntries[$fleetName] .= $this->customersService->getExportDataForCustomer($invoice->getCustomer()) . "\r\n";
                }
            }

            // Export invoices data
            $this->exportData($date, $invoicesEntries, self::TYPE_INVOICES, $path);

            // Export customers data
            $this->exportData($date, $customersEntries, self::TYPE_CUSTOMERS, $path);
        }

        if (!$this->noFtp) {
            ftp_close($this->ftpConn);
        }

        $this->logger->log(sprintf("%s;INF;exportRegistriesAction;end\n", date_create()->format('ymd His')));
    }

    /**
     * Retrieves invoices based on params and groups them as needed
     * @return array[]
     */
    private function retrieveData()
    {

        $invoices = null;
        $filterFleet = $this->request->getParam('fleet');
        if ($filterFleet !== null) {
            try {
                $filterFleet = $this->fleetService->getFleetByCode($filterFleet);
            } catch(FleetNotFoundException $e) {
                $this->logger->log(sprintf("%s;ERR;retrieveData;Use a valid fleet code!\n",
                    date_create()->format('ymd His')));

                exit;
            }
        }
        if ($this->all) {
            $this->logger->log(sprintf("%s;INF;retrieveData;all\n",
                date_create()->format('ymd His')));

            $invoices = $this->invoicesService->getInvoicesByFleetJoinCustomers($filterFleet, null);
        } else {
            $date = date_create($this->request->getParam('date') ?: 'yesterday');
            // validate date
            if ($date === false) {
                $this->logger->log(sprintf("%s;ERR;retrieveData;Please use a valid date format (eg. YYYY-MM-DD)\n",
                    date_create()->format('ymd His')));

                exit;
            }
            $this->logger->log(sprintf("%s;INF;retrieveData;date;%s\n",
                date_create()->format('ymd His'),
                $date->format('Y-m-d')));

            $invoices = $this->invoicesService->getInvoicesByDateAndFleetJoinCustomers($date, $filterFleet);
        }

        $this->logger->log(sprintf("%s;INF;retrieveData;count;%d\n",
            date_create()->format('ymd His'),
            count($invoices)));

        return $this->invoicesService->groupByInvoiceDate($invoices);
    }

    /**
     * @param \DateTime $date
     * @param string[] $entries
     * @param string $type
     * @param string $path
     */
    private function exportData(\DateTime $date, $entries, $type, $path)
    {
        if (!$this->dryRun && !$this->noInvoices && !empty($entries)) {

            foreach ($entries as $fleetName => $entry) {
                $fileName = $this->testName . "export" . $type . '_' . $date->format('Y-m-d') . ".txt";

                $this->logger->log(sprintf("%s;INF;exportData;%s;%s\n",
                    date_create()->format('ymd His'),
                    $type,
                    $path . $fleetName . '/' . $fileName));

                $this->ensurePathExistsLocally($path . $fleetName);
                $file = fopen($path . $fleetName . '/' . $fileName, 'w');
                fwrite($file, $entry);
                fclose($file);

                $this->exportToFtp($path . $fleetName . '/' . $fileName, $fleetName . '/' . $fileName);
            }
        }
    }

    /**
     * Checks wether path exists under data/export and creates it if it doesn't
     * @param string $path
     */
    private function ensurePathExistsLocally($path)
    {
        if (!file_exists($path)) {
            $this->logger->log("Generating local directory " . $path . " ... ");
            $this->logger->log(sprintf("%s;INF;ensurePathExistsLocally;path;%s\n",
                date_create()->format('ymd His'),
                $path));

            if (mkdir($path)) {
                $this->logger->log(sprintf("%s;INF;ensurePathExistsLocally;path;DONE\n",
                    date_create()->format('ymd His')));
            } else {
                $this->emailService->sendEmail(
                    $this->alertConfig['to'],
                    "Sharengo - export error",
                    "Error while creating local directory at path " . $path .
                    " Export was aborted"
                );
                $this->logger->log(sprintf("%s;ERR;ensurePathExistsLocally;path;FAIL\n",
                    date_create()->format('ymd His')));
                exit;
            }
        }
    }

    /**
     * Params expected to be relative paths like path/to/file/.../filename.txt
     * @param string $from
     * @param string $to
     */
    private function exportToFtp($from, $to)
    {
        if (!$this->noFtp) {
            if (ftp_put($this->ftpConn, $to, $from, FTP_ASCII)) {
                $this->logger->log(sprintf("%s;INF;exportToFtp;upload succes;%s;%s\n",
                    date_create()->format('ymd His'),
                    $from,
                    $to));
            } else {
                $this->emailService->sendEmail(
                    $this->alertConfig['to'],
                    "Sharengo - export error",
                    "The ftp connection was established but there was an error "
                    . "uploading file " . $from . " to " . $to
                );

                $this->logger->log(sprintf("%s;ERR;exportToFtp;upload FAIL;%s;%s\n",
                    date_create()->format('ymd His'),
                    $from,
                    $to));
            }
        }
    }

    /**
     * Attempts connection to ftp server
     * @param string[] $config
     */
    private function connectToServer($config)
    {
        if (!$this->noFtp) {
            $this->logger->log("Connecting to ftp server... ");
            $this->ftpConn = ftp_connect($config['server']);
            if (!$this->ftpConn) {
                $this->emailService->sendEmail(
                    $this->alertConfig['to'],
                    "Sharengo - export error",
                    "The ftp connection could not be established. Date: " .
                    date_create()->format('Y-m-d H:i:s') .
                    " Export was aborted!"
                );
                $this->logger->log(" Could not connect to ftp server! ...aborting export\n");
                die;
            }
            $login = ftp_login($this->ftpConn, $config['name'], $config['password']);
            ftp_pasv($this->ftpConn, true);
            $this->logger->log(" Connected!\n");
        }
    }
}
