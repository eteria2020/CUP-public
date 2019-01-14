<?php

namespace Application\Controller;

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
     * @param CustomersBonusPackagesService $customersBonusPackagesService
     * @param BuyCustomerBonusPackage $buyCustomerBonusPackage
     * @param CartasiContractsService $cartasiContractsService
     * @param Zend\Mvc\I18n\Translator $translator
     */
    public function __construct(
        CustomersBonusPackagesService $customersBonusPackagesService,
        BuyCustomerBonusPackage $buyCustomerBonusPackage,
        CartasiContractsService $cartasiContractsService,
        Translator $translator
    ) {
        $this->customersBonusPackagesService = $customersBonusPackagesService;
        $this->buyCustomerBonusPackage = $buyCustomerBonusPackage;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->translator = $translator;
    }

    public function packageAction()
    {
        $packageId = $this->params('id');
        $package = $this->customersBonusPackagesService->getBonusPackageById($packageId);
        $customer = $this->identity();
        $contract = $this->cartasiContractsService->getCartasiContract($customer);

        $viewModel = new ViewModel([
            'package' => $package,
            'hasContract' => $contract instanceof Contracts
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
                $this->flashMessenger()->addErrorMessage('Si è verificato un errore durante l\'acquisto del pacchetto richiesto');
            }
        }

        return new JsonModel();
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

}
