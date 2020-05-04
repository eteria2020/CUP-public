<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\EmailService;
use SharengoCore\Service\CustomersBonusPackagesService;
use SharengoCore\Entity\CustomersBonusPackages;
use SharengoCore\Entity\Customers;
//use SharengoCore\Entity\CustomersPoints;
use SharengoCore\Service\BuyCustomerBonusPackage;
use SharengoCore\Traits\CallableParameter;
use SharengoCore\Exception\PackageNotFoundException;
use SharengoCore\Exception\CustomerNotFoundException;
use Cartasi\Entity\Contracts;
use Cartasi\Service\CartasiContractsService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\I18n\Translator;
//use Zend\Mvc\Controller\Plugin\FlashMessenger;

class CustomerBonusPackagesController extends AbstractActionController
{
    use CallableParameter;

    /**
     * @var CustomersService
     */
    private $customerService;

    /**
     * @var CustomersBonusPackagesService
     */
    private $customersBonusPackagesService;

    /**
     * @var BuyCustomerBonusPackage
     */
    private $buyCustomerBonusPackage;

    /**
     * @var CartasiContractsService
     */
    private $cartasiContractsService;

    /**
     * @var \Zend\Mvc\I18n\Translator
     */
    private $translator;

    /**
     * @var array $config
     */
    private $config;

    /**
     * @var $serverInstance
     */
    private $serverInstance = "";

    /**
     * @var EmailService
     */
    private $emailService;
    
    /**
     * @param CustomersService $customersService
     * @param CustomersBonusPackagesService $customersBonusPackagesService
     * @param BuyCustomerBonusPackage $buyCustomerBonusPackage
     * @param CartasiContractsService $cartasiContractsService
     * @param Zend\Mvc\I18n\Translator $translator
     * @param EmailService $emailService
     * @param array $config
     */
    public function __construct(
        CustomersBonusPackagesService $customersBonusPackagesService,
        BuyCustomerBonusPackage $buyCustomerBonusPackage,
        CartasiContractsService $cartasiContractsService,
        Translator $translator,
        EmailService $emailService,
        array $config
    ) {
        $this->customersBonusPackagesService = $customersBonusPackagesService;
        $this->buyCustomerBonusPackage = $buyCustomerBonusPackage;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->translator = $translator;
        $this->emailService = $emailService;
        $this->config = $config;

        if(isset($this->config['serverInstance'])) {
            $this->serverInstance = $this->config['serverInstance'];
        }
    }

    public function packageAction()
    {
        $packageId = $this->params('id');
        $package = $this->customersBonusPackagesService->getBonusPackageById($packageId);
        $customer = $this->identity();
        $contract = $this->cartasiContractsService->getCartasiContract($customer);

        $serverInstance = (isset($this->serverInstance["id"])) ? $this->serverInstance["id"] : null;

        $viewModel = new ViewModel([
            'package' => $package,
            'hasContract' => $contract instanceof Contracts,
            'serverInstance' => $serverInstance,
        ]);

        return $viewModel->setTerminal(true);
    }

    public function buyPackageAction()
    {
        $packageId = $this->params()->fromPost('packageId');
        $package = $this->customersBonusPackagesService->getBonusPackageById($packageId);
        $customer = $this->identity();

        $exceptionMessage = $this->checkBonusPackagesRequest($customer, $package);

        if(!is_null($exceptionMessage)) {
            $this->flashMessenger()->addErrorMessage($exceptionMessage);
            throw new PackageNotFoundException();
        }

        // The customer could not be identified
        if (!$customer instanceof Customers) {
            $this->flashMessenger()->addErrorMessage($this->translator->translate('Impossibile completare l\'acquisto del pacchetto richiesto'));
            throw new CustomerNotFoundException();
        }

        // The customer did not pay the first payment
        if (!$customer->getFirstPaymentCompleted()) {
            $this->flashMessenger()->addErrorMessage($this->translator->translate('Occorre effettuare l\'acquisto del Pacchetto Benvenuto prima di poter acquistare un pacchetto'));

        } else {
            $success = $this->buyCustomerBonusPackage($customer, $package);

            if ($success) {
                $this->flashMessenger()->addSuccessMessage($this->translator->translate('Acquisto del pacchetto completato correttamente'));
            } else {
                $this->flashMessenger()->addErrorMessage($this->translator->translate("Si è verificato un errore durante l'acquisto del pacchetto richiesto"));
            }
        }

        return new JsonModel();
    }
    
    // MYSHARENGO
    public function myBuyPackageAction()
    {
        $packageId = $this->params()->fromPost('packageId');
        $package = $this->customersBonusPackagesService->getBonusPackageById($packageId);
        
        $customer = $this->identity();

        $exceptionMessage = $this->checkBonusPackagesRequest($customer, $package);

        if(!is_null($exceptionMessage)) {
            $this->flashMessenger()->addErrorMessage($exceptionMessage);
            throw new PackageNotFoundException();
        }

        // The customer could not be identified
        if (!$customer instanceof Customers) {
            $this->flashMessenger()->addErrorMessage($this->translator->translate('Impossibile completare la prenotazione del canone richiesto'));
            throw new CustomerNotFoundException();
        }
        
        $success = $this->buyCustomerBonusPackage($customer, $package, true);

        if ($success) {

            $this->sendEmail($customer->getEmail(), $package, $customer->getLanguage(), 30);
            $this->sendNotify($customer, $package);

            $this->flashMessenger()->addSuccessMessage($this->translator->translate('Prenotazione del canone completata correttamente'));
        } else {
            $this->flashMessenger()->addErrorMessage($this->translator->translate("Si è verificato un errore durante la prenotazione del canone richiesto"));
        }

        return new JsonModel();
    }
    public function myPackageAction()
    {
        $packageId = $this->params('id');
        $package = $this->customersBonusPackagesService->getBonusPackageById($packageId);
        $customer = $this->identity();
        //$contract = $this->cartasiContractsService->getCartasiContract($customer);

        $serverInstance = (isset($this->serverInstance["id"])) ? $this->serverInstance["id"] : null;

        $viewModel = new ViewModel([
            'package' => $package,
            'serverInstance' => $serverInstance,
        ]);

        return $viewModel->setTerminal(true);
    }
    
    /**
     * Check the data before to assign package
     * 
     * @param Customers $customer
     * @param CustomersBonusPackages $package
     * @throws PackageNotFoundException
     */
    private function checkBonusPackagesRequest(Customers $customer, CustomersBonusPackages $package) {
        $result = null;

        // The packageId is incorrect
        if (!$package instanceof CustomersBonusPackages) {
            return $this->translator->translate('Impossibile completare l\'acquisto del pacchetto richiesto');
        }

        if (!$customer instanceof Customers) {
            return $this->translator->translate('Utente non riconosciuto');
        }

        if(!in_array($package, $this->customersBonusPackagesService->getAvailableBonusPackges())) {
            return $this->translator->translate('Pacchetto non valido');
        }

        if($package->getType()=='Pacchetto') {

        } elseif($package->getType()=='PacchettoPunti') {
            if($customer->getResidualPoints()<$package->getCost()) {
                return $this->translator->translate($this->translator->translate('Punti ossigeno insufficienti'));
            }
        } else {
            return $this->translator->translate($this->translator->translate('Pacchetto non acquistabile'));
        }

        return $result;
    }
    
    private function sendEmail($email, CustomersBonusPackages $package, $language, $category) {
        //$writeTo = $this->emailSettings['from'];
        $mail = $this->emailService->getMail($category, $language);
        $content = sprintf(
                $mail->getContent(), $package->getName()
        );

        //file_get_contents(__DIR__.'/../../../view/emails/parkbonus_pois-it_IT.html'),

        $attachments = [
                //'bannerphono.jpg' => __DIR__.'/../../../../../public/images/bannerphono.jpg'
        ];
        $this->emailService->sendEmail(
                $email, //send to
                $mail->getSubject(), //'Share’ngo: bonus 5 minuti',//object email
                $content, $attachments
        );
    }
    
    private function sendNotify(Customers $customer, CustomersBonusPackages $package) {
        //$writeTo = $this->emailSettings['from'];
        $content = sprintf('Il cliente %1$s con l\'id %2$s ha prenotato il pacchetto %3$s con il codice %4$s.', 
                $customer->getName(),
                $customer->getId(),
                $package->getName(),
                $package->getCode()
        );
        //file_get_contents(__DIR__.'/../../../view/emails/parkbonus_pois-it_IT.html'),

        $attachments = [
                //'bannerphono.jpg' => __DIR__.'/../../../../../public/images/bannerphono.jpg'
        ];
        $this->emailService->sendEmail(
                'mysharengo@sharengo.eu', //send to
                'MYSHARENGO: Notifica avvenuta prenotazione canone', //'Share’ngo: bonus 5 minuti',//object email
                $content, $attachments
        );
    }
}
