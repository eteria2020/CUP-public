<?php

namespace Application\Controller;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\CustomerNoteService;
use SharengoCore\Service\UsersService;
//use SharengoCore\Entity\Webuser;
use SharengoCore\Service\EmailService;

use MvLabsDriversLicenseValidation\Service\PortaleAutomobilistaValidationService;
use SharengoCore\Service\DriversLicenseValidationService;
use SharengoCore\Service\CountriesService;
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

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var PortaleAutomobilistaValidationService
     */
    private $portaleAutomobilistaValidationService;

    /**
     * @var DriversLicenseValidationService
     */
    private $driversLicenseValidationService;

    /**
     * @var CountriesService
     */
    private $countriesService;

    public function __construct(
        CustomersService $customersService,
        CustomerDeactivationService $customerDeactivationService,
        CustomerNoteService $customerNoteService,
        UsersService $usersService,
        Logger $logger,
        EmailService $emailService,
        PortaleAutomobilistaValidationService $portaleAutomobilistaValidationService,
        DriversLicenseValidationService $driversLicenseValidationService,
        CountriesService $countriesService
    ) {
        $this->customersService = $customersService;
        $this->customerDeactivationService = $customerDeactivationService;
        $this->customerNoteService = $customerNoteService;
        $this->usersService = $usersService;
        $this->logger = $logger;
        $this->emailService = $emailService;
        $this->portaleAutomobilistaValidationService = $portaleAutomobilistaValidationService;
        $this->driversLicenseValidationService = $driversLicenseValidationService;
        $this->countriesService = $countriesService;
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

            $this->customerDeactivationForDriverLicenseProblem(
                $customer,
                "Messaggio di sistema: utente disattivato per patente scaduta.",
                10);

        }

        $this->logger->log("\nEnd time = " . date_create()->format('Y-m-d H:i:s') ."\n");
    }

    private function sendEmail($email, $name, $language, $mailCategory)
    {
        $mail = $this->emailService->getMail($mailCategory, $language);
        $content = sprintf(
            $mail->getContent(),
            $name
        );

        $attachments = [];
        $this->emailService->sendEmail(
            $email,
            $mail->getSubject(),
            $content,
            $attachments
        );
    }

     /**
     * Periodic check of italian's driver license (no foreign license).
     * We select the oldest driver license of active customers width check old and repeat the check.
     */
    public function periodicCheckValidLicenseAction() {

        $this->logger->log(sprintf("%s;INF;periodicCheckDriverLicenseAction;start\n", date_create()->format('y-m-d H:i:s')));

        $customersId = $this->customersService->getCustomersValidLicenseOldCheck(null, 10);

        foreach($customersId as $customerId){
            $customer =$this->customersService->findById($customerId['id']);
            $checkResult = $this->periodicCheckValidLicenseCustomer($customer);

            $this->logger->log(sprintf("%s;INF;periodicCheckDriverLicenseAction;event;%s;%s;%s\n",
                date_create()->format('y-m-d H:i:s'),
                $customer->getId(),
                $customer->getEmail(),
                $checkResult));
        }

        $this->logger->log(sprintf("%s;INF;periodicCheckDriverLicenseAction;end\n", date_create()->format('y-m-d H:i:s')));
    }

    /**
     * Deactivate the customer, send an email and add a note width the deactivations reason.
     *
     * @param Customers $customer
     * @param string $message
     * @param integer $mailCategory
     */
    private function customerDeactivationForDriverLicenseProblem(Customers $customer, $message, $mailCategory) {
        //CustomerNoteService.php in service sharengo-coremodule
        $webuser = $this->usersService->findUserById(12);
        $this->customerNoteService->addNote($customer, $webuser ,$message);

        if($mailCategory==4) {
            $this->customerDeactivationService->deactivateForDriversLicense($customer);
        } else if($mailCategory==10){
            $this->customerDeactivationService->deactivateForExpiredDriversLicense($customer);
        }

        $this->sendEmail(
            $customer->getEmail(),
            $customer->getName(),
            $customer->getLanguage(),
            $mailCategory);

    }

    /**
     * Check driver's license of customer
     *
     * @param Customers $customer
     * @return boolean
     */
     private function periodicCheckValidLicenseCustomer(Customers $customer) {
        $result=false;

        $data = [
            'email' => $customer->getEmail(),
            'driverLicense' => $customer->getDriverLicense(),
            'taxCode' => $customer->getTaxCode(),
            'driverLicenseName' => $customer->getDriverLicenseName(),
            'driverLicenseSurname' => $customer->getDriverLicenseSurname(),
            'birthDate' => ['date' => $customer->getBirthDate()->format('Y-m-d')],
            'birthCountry' => $customer->getBirthCountry(),
            'birthProvince' => $customer->getBirthProvince(),
            'birthTown' => $customer->getBirthTown()
        ];

        $data['birthCountryMCTC'] = $this->countriesService->getMctcCode($data['birthCountry']);
        $data['birthProvince'] = $this->driversLicenseValidationService->changeProvinceForValidationDriverLicense($data);

        $response = $this->portaleAutomobilistaValidationService->validateDriversLicense($data);
        $this->driversLicenseValidationService->addFromResponse($customer, $response, $data);
        if ($response->valid()) {
            $result = true;
        } else {
            $this->customerDeactivationForDriverLicenseProblem(
                $customer,
                "Messaggio di sistema: controllo periodico, patente non valida.",
                4);
        }
        //$this->getEventManager()->trigger('driversLicenseEdited', $this, $params);

         return $result;
     }
}
