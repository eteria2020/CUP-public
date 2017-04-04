<?php

namespace Application\Controller;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\CustomerNoteService;
use SharengoCore\Service\UsersService;
use SharengoCore\Entity\Webuser;

use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class DisableCustomerController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var CustomerDeactivationService
     */
    private $customerDeactivationService;
    
     /**
     * @var CustomerNotesService
     */
    private $customerNoteService;
    /**
     * @var UserService 
     */
    private $usersService;

    /**
     * @var Logger
     */
    private $logger;
    
    public function __construct(
        CustomersService $customersService,
        CustomerDeactivationService $customerDeactivationService,
        CustomerNoteService $customerNoteService,
        UsersService $usersService,
        Logger $logger
    ) {
        $this->customersService = $customersService;
        $this->customerDeactivationService = $customerDeactivationService;
        $this->customerNoteService = $customerNoteService;
        $this->usersService = $usersService;
        $this->logger = $logger;
    }

    public function invalidDriversLicenseAction()
    {
        $customerId = $this->params('customerId');
        if (!is_numeric($customerId)) {
            fwrite(STDOUT, 'You need to provide a valid numeric id to retrieve the customer'.PHP_EOL);
            exit;
        }

        $customer = $this->customersService->findById($customerId);
        if (!$customer instanceof Customers) {
            fwrite(STDOUT, 'The id you provided is not associated to any customer'.PHP_EOL);
            exit;
        }

        $this->customerDeactivationService->deactivateForDriversLicense($customer);

        fwrite(STDOUT, 'The customer '.$customer->getName().' '.$customer->getSurname().' was disabled correctly'.PHP_EOL);
    }

    private function prepareLogger()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);
    }
    
    public function expiredDriversLicenseAction(){
        
        $request = $this->getRequest();
        $debug = $request->getParam('debug-mode') || $request->getParam('dm');
        
        $this->prepareLogger();
        
        $customers = $this->customersService->getCustomersExpiredLicense();
        
        $this->logger->log("\nStarted time = " . date_create()->format('Y-m-d H:i:s') . " - Count expired: ". count($customers)."\n");
        
        if ($debug) {
            $this->logger->log("Debug mode: \n");
        }
        
        foreach ($customers as $customer) {
        
            if (!$customer instanceof Customers) {
               continue;
            }

            $this->logger->log(date('Y-m-d H:i:s').";". $customer->getId() .";". $customer->getDriverLicenseExpire()->format('Y-m-d') ."\n");
            
            if ($debug) {
                continue;
            }
            
            $this->customerDeactivationService->deactivateForExpiredDriversLicense($customer);

            //CustomerNoteService.php in service sharengo-coremodule
            $webuser = $this->usersService->findUserById(12); 

            $this->customerNoteService->addNote($customer, $webuser ,"Messaggio di sistema: utente disattivato per patente scaduta.");
        
        }
        
        $this->logger->log("\nEnd time = " . date_create()->format('Y-m-d H:i:s') ."\n");
    }
}
