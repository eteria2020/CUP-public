<?php

namespace Application\Controller;

use Application\Form\PromoCodeForm;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\CustomersBonus;
use SharengoCore\Entity\PromoCodes;
use SharengoCore\Exception\BonusAssignmentException;
use SharengoCore\Exception\NotAValidCodeException;
use SharengoCore\Exception\CodeAlreadyUsedException;
use SharengoCore\Service\BonusService;
use SharengoCore\Service\CarrefourService;
use SharengoCore\Service\CustomersBonusPackagesService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\PromoCodesOnceService;

use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class AdditionalServicesController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customerService;

    /**
     * @var CarrefourService
     */
    private $carrefourService;

    /**
     * @var PromoCodeForm
     */
    private $promoCodeForm;

    /**
     * @var PromoCodesService
     */
    private $promoCodeService;

    /**
     * @var PromoCodesOnceService
     */
    private $promoCodeOnceService;

    /**
     * @var CustomersBonusPackagesService
     */
    private $customersBonusPackagesService;

    /**
     * @var AuthenticationService
     */
    private $authService;
    /**
     * @var BonusService
     */
    private $bonusService;

    /**
     * @param CustomersService $customerService
     * @param CarrefourService $carrefourService
     * @param Form $promoCodeForm
     * @param PromoCodesService $promoCodeService
     * @param CustomersBonusPackagesService $customersBonusPackagesService
     * @param AuthenticationService $authService
     * @param BonusService $bonusService
     */
    public function __construct(
        CustomersService $customersService,
        CarrefourService $carrefourService,
        Form $promoCodeForm,
        PromoCodesService $promoCodeService,
        PromoCodesOnceService $promoCodeOnceService,
        CustomersBonusPackagesService $customersBonusPackagesService,
        AuthenticationService $authService,
        BonusService $bonusService
    ) {
        $this->customersService = $customersService;
        $this->carrefourService = $carrefourService;
        $this->promoCodeForm = $promoCodeForm;
        $this->promoCodeService =  $promoCodeService;
        $this->promoCodeOnceService =  $promoCodeOnceService;
        $this->customersBonusPackagesService = $customersBonusPackagesService;
        $this->authService = $authService;
        $this->bonusService = $bonusService;
    }

    public function additionalServicesAction()
    {
        //if there is mobile param change layout
        $mobile = $this->params()->fromRoute('mobile');
        if ($mobile) {
            $this->layout('layout/map');
        }
        $form = $this->promoCodeForm;

        if ($this->getRequest()->isPost()) {
            $customer = $this->authService->getIdentity();
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {

                $code = $postData['promocode']['promocode'];

                if ($this->promoCodeService->isStandardPromoCode($code)) {

                    try {
                        $promoCode = $this->promoCodeService->getPromoCode($code);
                        $this->customersService->addBonusFromPromoCode($customer, $promoCode);
                        $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                    } catch (BonusAssignmentException $e) {
                        $this->flashMessenger()->addErrorMessage($e->getMessage());
                    } catch (\Exception $e) {
                        $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo STD.');
                    }

                } else if ($this->promoCodeOnceService->isValid($code)) {
                    try {
                        $this->promoCodeOnceService->usePromoCode($customer, $code);
                        $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                    } catch (\Exception $ex) {
                        $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo PCO.');
                    }
                }
                else {
                    try {
                        $this->carrefourService->addFromCode($customer, $code);
                        $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                    } catch(NotAValidCodeException $ex){
                        $this->flashMessenger()->addErrorMessage('Promocode non valido.');
                    } catch(CodeAlreadyUsedException $ex){
                        $this->flashMessenger()->addErrorMessage('Promocode già utilizzato.');
                    } catch (\Exception $e) {
                        $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo CR.');
                    }
                }

                return $this->redirect()->toRoute('area-utente/additional-services');
            }
        }
        
        $bonusPackages = $this->customersBonusPackagesService->getAvailableBonusPackges();
        $customer = $this->authService->getIdentity();
        $verify = count($this->bonusService->verifyWomenBonus($customer));
        
        return new ViewModel([
            'promoCodeForm' => $form,
            'bonusPackages' => $bonusPackages,
            'customer' => $customer,
            'verify' => $verify,
            'mobile' => $mobile
        ]);
    }

    public function giftPackagesAction()
    {
        $form = $this->promoCodeForm;

        if ($this->getRequest()->isPost()) {
            $customer = $this->authService->getIdentity();
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {

                $code = $postData['promocode']['promocode'];

                if ($this->promoCodeService->isStandardPromoCode($code)) {

                    try {
                        $promoCode = $this->promoCodeService->getPromoCode($code);
                        $this->customersService->addBonusFromPromoCode($customer, $promoCode);
                        $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                    } catch (BonusAssignmentException $e) {
                        $this->flashMessenger()->addErrorMessage($e->getMessage());
                    } catch (\Exception $e) {
                        $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo STD.');
                    }

                } else if ($this->promoCodeOnceService->isValid($code)) {
                    try {
                        $this->promoCodeOnceService->usePromoCode($customer, $code);
                        $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                    } catch (\Exception $ex) {
                        $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo PCO.');
                    }
                }
                else {
                    try {
                        $this->carrefourService->addFromCode($customer, $code);
                        $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                    } catch(NotAValidCodeException $ex){
                        $this->flashMessenger()->addErrorMessage('Promocode non valido.');
                    } catch(CodeAlreadyUsedException $ex){
                        $this->flashMessenger()->addErrorMessage('Promocode già utilizzato.');
                    } catch (\Exception $e) {
                        $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo CR.');
                    }
                }

                return $this->redirect()->toRoute('area-utente/gift-packages');
            }
        }

        $bonusPackages = $this->customersBonusPackagesService->getAvailableBonusPackges();

        return new ViewModel([
            'promoCodeForm' => $form,
            'bonusPackages' => $bonusPackages
        ]);
    }

}
