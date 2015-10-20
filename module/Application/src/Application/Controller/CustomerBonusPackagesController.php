<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersBonusPackagesService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class CustomerBonusPackagesController extends AbstractActionController
{
    /**
     * @var CustomersBonusPackagesService
     */
    private $customersBonusPackagesService;

    public function __construct(
        CustomersBonusPackagesService $customersBonusPackagesService
    ) {
        $this->customersBonusPackagesService = $customersBonusPackagesService;
    }

    public function packageAction()
    {
        $packageId = $this->params('id');

        $package = $this->customersBonusPackagesService->getBonusPackageById($packageId);

        $viewModel = new ViewModel([
            'package' => $package
        ]);

        return $viewModel->setTerminal(true);
    }

    public function buyPackageAction()
    {
        $packageId = $this->params()->fromPost('packageId');

        $package = $this->customersBonusPackagesService->getBonusPackageById($packageId);

        return new JsonModel([
            'done' => true
        ]);
    }
}
