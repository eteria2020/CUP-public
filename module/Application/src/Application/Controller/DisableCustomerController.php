<?php

namespace Application\Controller;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\CustomerNoteService;
use SharengoCore\Service\UsersService;
use SharengoCore\Entity\Webuser;

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
    
    private $usersService;

    public function __construct(
        CustomersService $customersService,
        CustomerDeactivationService $customerDeactivationService,
        CustomerNoteService $customerNoteService,
        UsersService $usersService
    ) {
        $this->customersService = $customersService;
        $this->customerDeactivationService = $customerDeactivationService;
        $this->customerNoteService = $customerNoteService;
        $this->usersService = $usersService;
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
    
    public function expiredDriversLicenseAction(){
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

        $this->customerDeactivationService->deactivateForExpiredDriversLicense($customer);

        fwrite(STDOUT, 'The customer '.$customer->getName().' '.$customer->getSurname().' was disabled correctly'.PHP_EOL);
        //CustomerNoteService.php in service sharengo-coremodule
        $webuser = $this->usersService->findUserById(3); 

        $this->customerNoteService->addNote($customer, $webuser ,"Test nota");
        
    }
}
