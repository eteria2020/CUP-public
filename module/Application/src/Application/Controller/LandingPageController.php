<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

// Internals
use SharengoCore\Service\CustomersBonusPackagesService;

// Externals
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LandingPageController extends AbstractActionController
{
    /**
     * @var BonusPackagesService
     */
    private $customersBonusPackagesService;

    /**
     * LandingPageController constructor.
     * @param CustomersBonusPackagesService $customersBonusPackagesService
     */
    public function __construct(
        CustomersBonusPackagesService $customersBonusPackagesService
    ) {
        $this->customersBonusPackagesService = $customersBonusPackagesService;
    }


    public function linearAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function linkAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function lumsaAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function madamaAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function marketAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function maxxiAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function notrunningminAction()
    {
        $userId = $this->params()->fromRoute('userId');
        return (new viewModel(['userId' => $userId]))->setTerminal(true);
    }

    public function bestriderpackAction()
    {
        $userId = $this->params()->fromRoute('userId');
        return (new viewModel(['userId' => $userId]))->setTerminal(true);
    }

    public function smartpackAction()
    {
        $userId = $this->params()->fromRoute('userId');
        return (new viewModel(['userId' => $userId]))->setTerminal(true);
    }

    public function fastridepackAction()
    {
        $userId = $this->params()->fromRoute('userId');
        return (new viewModel(['userId' => $userId]))->setTerminal(true);
    }

    public function acceptpackAction()
    {
        $userId = $this->params()->fromRoute('userId');
        $packageId = $this->params()->fromRoute('package');
        $package = $this->customersBonusPackagesService->getBonusPackageById($packageId);
        return (new viewModel(['userId' => $userId, 'package' => $package]))->setTerminal(true);
    }

    public function freebonusokAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function freebonuskoAction()
    {
        $msg = $this->params()->fromRoute('msg');
        return (new viewModel(['msg' => $msg]))->setTerminal(true);
    }

    public function ordingAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function ordproAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function paybackAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function politecnicoAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function pwcAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function romaAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function sabaAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function scoac1Action()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function scuolecivicheAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function sim1Action()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function svoltaAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function teatroElfoAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function tevereAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function unirm1Action()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function vipzipAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function volontariocardAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function wave5Action()
    {
        return (new viewModel())->setTerminal(true);
    }
}
