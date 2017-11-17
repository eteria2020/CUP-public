<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersBonusPackagesService;
use SharengoCore\Entity\CustomersBonusPackages;
use SharengoCore\Entity\Customers;
use SharengoCore\Service\BuyCustomerBonusPackage;
use SharengoCore\Traits\CallableParameter;
use SharengoCore\Exception\PackageNotFoundException;
use SharengoCore\Exception\CustomerNotFoundException;
use Cartasi\Entity\Contracts;
use Cartasi\Service\CartasiContractsService;
use SharengoCore\Service\CustomersPointsService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

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
     * @var CustomersPointsService
     */
    private $customersPointsService;

    /**
     * @param CustomersBonusPackagesService $customersBonusPackagesService
     * @param BuyCustomerBonusPackage $buyCustomerBonusPackage
     * @param CartasiContractsService $cartasiContractsService
     * @param CustomersPointsService $customersPointsService
     */
    public function __construct(
        CustomersBonusPackagesService $customersBonusPackagesService,
        BuyCustomerBonusPackage $buyCustomerBonusPackage,
        CartasiContractsService $cartasiContractsService,
        CustomersPointsService $customersPointsService
    ) {
        $this->customersBonusPackagesService = $customersBonusPackagesService;
        $this->buyCustomerBonusPackage = $buyCustomerBonusPackage;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->customersPointsService = $customersPointsService;
    }

    public function packageAction()
    {
        $packageId = $this->params('id');

        $package = $this->customersBonusPackagesService->getBonusPackageById($packageId);

        $customer = $this->identity();
        if($package->getType() === "Pacchetto"){
            $contract = $this->cartasiContractsService->getCartasiContract($customer);
        }else{
            $contractPoint = $this->customersPointsService->buyPacketPoints($customer);
        }

        $viewModel = new ViewModel([
            'package' => $package,
            'hasContract' => (($package->getType() === "Pacchetto") ? $contract instanceof Contracts : $contractPoint)
        ]);

        return $viewModel->setTerminal(true);
    }

    public function buyPackageAction()
    {
        $packageId = $this->params()->fromPost('packageId');

        $package = $this->customersBonusPackagesService->getBonusPackageById($packageId);

        // The packageId is incorrect
        if (!$package instanceof CustomersBonusPackages) {
            $this->flashMessenger()->addErrorMessage('Impossibile completare l\'acquisto del pacchetto richiesto');
            throw new PackageNotFoundException();
        }

        $customer = $this->identity();

        // The customer could not be identified
        if (!$customer instanceof Customers) {
            $this->flashMessenger()->addErrorMessage('Impossibile completare l\'acquisto del pacchetto richiesto');
            throw new CustomerNotFoundException();
        }

        // The customer did not pay the first payment
        if (!$customer->getFirstPaymentCompleted()) {
            $this->flashMessenger()->addErrorMessage('Occorre effettuare il pagamento per l\'iscrizione al servizio prima di poter acquistare un pacchetto');

        } else {
            $success = $this->buyCustomerBonusPackage($customer, $package);

            if ($success) {
                $this->flashMessenger()->addSuccessMessage('Acquisto del pacchetto bonus completato correttamente');
            } else {
                $this->flashMessenger()->addErrorMessage('Si è verificato un errore durante l\'acquisto del pacchetto richiesto');
            }
        }

        return new JsonModel();
    }
}
