<?php

namespace Application\Controller;

use Application\Form\PromoCodeForm;
//use SharengoCore\Entity\Customers;
//use SharengoCore\Entity\CustomersBonus;
//use SharengoCore\Entity\PromoCodes;
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
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\ViewModel;

class AdditionalServicesController extends AbstractActionController {

    /**
     * @var Traslator
     */
    private  $translator;

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
     * @var array
     */
    private $config;

    /**
     * @var $serverInstance
     */
    private $serverInstance = "";

    /**
     * AdditionalServicesController constructor.
     * @param Translator $translator
     * @param CustomersService $customersService
     * @param CarrefourService $carrefourService
     * @param Form $promoCodeForm
     * @param PromoCodesService $promoCodeService
     * @param PromoCodesOnceService $promoCodeOnceService
     * @param CustomersBonusPackagesService $customersBonusPackagesService
     * @param AuthenticationService $authService
     * @param BonusService $bonusService
     * @param TripsService $tripsService
     * @param array $config
     */
    public function __construct(
        Translator $translator,
        CustomersService $customersService,
        CarrefourService $carrefourService,
        Form $promoCodeForm,
        PromoCodesService $promoCodeService,
        PromoCodesOnceService $promoCodeOnceService,
        CustomersBonusPackagesService $customersBonusPackagesService,
        AuthenticationService $authService,
        BonusService $bonusService,
        TripsService $tripsService,
        array $config
    ) {
        $this->translator = $translator;
        $this->customersService = $customersService;
        $this->carrefourService = $carrefourService;
        $this->promoCodeForm = $promoCodeForm;
        $this->promoCodeService = $promoCodeService;
        $this->promoCodeOnceService = $promoCodeOnceService;
        $this->customersBonusPackagesService = $customersBonusPackagesService;
        $this->authService = $authService;
        $this->bonusService = $bonusService;
        $this->tripsService = $tripsService;
        $this->config = $config;

        if(isset($this->config['serverInstance'])) {
            $this->serverInstance = $this->config['serverInstance'];
        }
    }

    public function additionalServicesAction() {
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
                        $this->flashMessenger()->addSuccessMessage($this->translator->translate('Operazione completata con successo!'));
                    } catch (BonusAssignmentException $e) {
                        $this->flashMessenger()->addErrorMessage($e->getMessage());
                    } catch (\Exception $e) {
                        $this->flashMessenger()->addErrorMessage($this->translator->translate('Si è verificato un errore applicativo STD.'));
                    }
                } elseif ($this->promoCodeOnceService->isValid($code)) {
                    try {
                        $this->promoCodeOnceService->usePromoCode($customer, $code);
                        $this->flashMessenger()->addSuccessMessage($this->translator->translate('Operazione completata con successo!'));
                    } catch (\Exception $ex) {
                        $this->flashMessenger()->addErrorMessage($this->translator->translate('Si è verificato un errore applicativo PCO.'));
                    }
                } else {
                    try {
                        $this->carrefourService->addFromCode($customer, $code);
                        $this->flashMessenger()->addSuccessMessage($this->translator->translate('Operazione completata con successo!'));
                    } catch (NotAValidCodeException $ex) {
                        $this->flashMessenger()->addErrorMessage($this->translator->translate('Promocode non valido.'));
                    } catch (CodeAlreadyUsedException $ex) {
                        $this->flashMessenger()->addErrorMessage($this->translator->translate('Promocode già utilizzato.'));
                    } catch (\Exception $e) {
                        $this->flashMessenger()->addErrorMessage($this->translator->translate('Si è verificato un errore applicativo CR.'));
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
        if ($verifyWelcomePackage == 0 && $customer->getFirstPaymentCompleted() && $verifyFirstTrip) {
            $showWelcomePackage = true;
        }

        $serverInstance = (isset($this->serverInstance["id"])) ? $this->serverInstance["id"] : null;

        return new ViewModel([
            'promoCodeForm' => $form,
            'bonusPackages' => $bonusPackages,
            'customer' => $customer,
            'verifyWomenVoucher' => $verifyWomenVoucher,
            'mobile' => $mobile,
            'showWelcomePackage' => $showWelcomePackage,
            'serverInstance' => $serverInstance,
        ]);
    }
}
