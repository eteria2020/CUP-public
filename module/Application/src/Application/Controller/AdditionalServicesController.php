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
use SharengoCore\Service\TripsService;
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
     * @var TripsService
     */
    private $tripsService;

    /**
     * @param CustomersService $customerService
     * @param CarrefourService $carrefourService
     * @param Form $promoCodeForm
     * @param PromoCodesService $promoCodeService
     * @param CustomersBonusPackagesService $customersBonusPackagesService
     * @param AuthenticationService $authService
     * @param BonusService $bonusService
     * @param TripsService $tripsService
     */
    public function __construct(
        CustomersService $customersService,
        CarrefourService $carrefourService,
        Form $promoCodeForm,
        PromoCodesService $promoCodeService,
        PromoCodesOnceService $promoCodeOnceService,
        CustomersBonusPackagesService $customersBonusPackagesService,
        AuthenticationService $authService,
        BonusService $bonusService,
        TripsService $tripsService
    ) {
        $this->customersService = $customersService;
        $this->carrefourService = $carrefourService;
        $this->promoCodeForm = $promoCodeForm;
        $this->promoCodeService =  $promoCodeService;
        $this->promoCodeOnceService =  $promoCodeOnceService;
        $this->customersBonusPackagesService = $customersBonusPackagesService;
        $this->authService = $authService;
        $this->bonusService = $bonusService;
        $this->tripsService = $tripsService;
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

        $verifyWomenVoucher = count($this->bonusService->verifyWomenBonusPackage($customer));

        /* Benvenuto Package */
        $verifyWelcomePackage = count($this->bonusService->verifyWelcomeBonusPackage($customer));
        $firstTrip = $this->tripsService->getFirstTripInvoicedByCustomer($customer);
        $verifyFirstTrip = false;
        if (count($firstTrip) == 1) {
            $now = new \DateTime();
            $firstTripDate = clone $firstTrip->getTimestampBeginning();
            $firstTripDateYear = $firstTrip->getTimestampBeginning()->add(new \DateInterval("P1Y")); //first invoiced trip + 1 year
            if ($now >= $firstTripDate && $now <= $firstTripDateYear) {
                $verifyFirstTrip = true;
            }
        }
        $showWelcomePackage = false;
        if ($verifyWelcomePackage == 0 && $customer->getFirstPaymentCompleted() && $verifyFirstTrip){
            $showWelcomePackage = true;
        }

        return new ViewModel([
            'promoCodeForm' => $form,
            'bonusPackages' => $bonusPackages,
            'customer' => $customer,
            'verifyWomenVoucher' => $verifyWomenVoucher,
            'mobile' => $mobile,
            'showWelcomePackage' => $showWelcomePackage,
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
