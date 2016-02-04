<?php

namespace Application\Controller;

use Application\Form\PromoCodeForm;
use SharengoCore\Entity\CustomersBonus;
use SharengoCore\Entity\PromoCodes;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\TripsService;
use Application\Form\DriverLicenseForm;

use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\EventManager\EventManager;

use SharengoCore\Service\CustomersService;
use SharengoCore\Entity\Customers;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Entity\Invoices;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Exception\BonusAssignmentException;
use SharengoCore\Service\CustomersBonusPackagesService as BonusPackagesService;

use Cartasi\Service\CartasiPaymentsService;
use Cartasi\Service\CartasiContractsService;

class UserAreaController extends AbstractActionController
{
    const SUCCESS_MESSAGE = 'Operazione eseguita con successo!';

    /**
     * @var CustomersService
     */
    private $I_customersService;

    /**
     * @var TripsService
     */
    private $I_tripsService;

    /**
    * @var \Zend\Authentication\AuthenticationService
    */
    private $userService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var Customers
     */
    private $customer;

    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    private $hydrator;

    /**
     * @var \Zend\Form\Form
     */
    private $profileForm;

    /**
     * @var \Zend\Form\Form
     */
    private $passwordForm;

    /**
     * @var \Zend\Form\Form
     */
    private $typeForm;

    /**
     * @var \Zend\Form\Form
     */
    private $driverLicenseForm;

    /**
     * @var Cartasi\Service\CartasiPaymentsService
     */
    private $cartasiPaymentsService;

    /**
     * @var boolean
     */
    private $showError = false;

    /**
     * @var PromoCodeForm
     */
    private $promoCodeForm;

    /**
     * @var PromoCodesService
     */
    private $promoCodeService;

    /**
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @var CartasiContractsService
     */
    private $cartasiContractsService;

    /**
     * @var BonusPackagesService
     */
    private $bonusPackagesService;

    /**
     * @var BannerJsonpUrl
     */
    private $bannerJsonpUrl;

    /**
     * @param CustomersService $I_customersService
     * @param TripsService $I_tripsService
     * @param AuthenticationService $userService
     * @param InvoicesService $invoicesService
     * @param Form $profileForm
     * @param Form $passwordForm
     * @param Form $driverLicenseForm
     * @param Form $promoCodeForm
     * @param HydratorInterface $hydrator
     * @param CartasiPaymentsService $cartasiPaymentsService
     * @param PromoCodesService $promoCodeService
     * @param TripPaymentsService $tripPaymentsService
     * @param CartasiContractsService $cartasiContractsService
     * @param BonusPackagesService $bonusPackagesService
     * @param $bannerJsonpUrl
     */
    public function __construct(
        CustomersService $I_customersService,
        TripsService $I_tripsService,
        AuthenticationService $userService,
        InvoicesService $invoicesService,
        Form $profileForm,
        Form $passwordForm,
        Form $driverLicenseForm,
        Form $promoCodeForm,
        HydratorInterface $hydrator,
        CartasiPaymentsService $cartasiPaymentsService,
        PromoCodesService $promoCodeService,
        TripPaymentsService $tripPaymentsService,
        CartasiContractsService $cartasiContractsService,
        BonusPackagesService $bonusPackagesService,
        $bannerJsonpUrl
    ) {
        $this->I_customersService = $I_customersService;
        $this->I_tripsService = $I_tripsService;
        $this->userService = $userService;
        $this->invoicesService = $invoicesService;
        $this->customer = $userService->getIdentity();
        $this->profileForm = $profileForm;
        $this->passwordForm = $passwordForm;
        $this->driverLicenseForm = $driverLicenseForm;
        $this->promoCodeForm = $promoCodeForm;
        $this->hydrator = $hydrator;
        $this->cartasiPaymentsService = $cartasiPaymentsService;
        $this->promoCodeService =  $promoCodeService;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->bonusPackagesService = $bonusPackagesService;
        $this->bannerJsonpUrl = $bannerJsonpUrl;
    }

    public function indexAction()
    {
        // check wether the customer still needs to register a credit card
        $customer = $this->userService->getIdentity();
        if ($this->I_customersService->isFirstTripManualPaymentNeeded($customer)) {
            $this->redirect()->toUrl($this->url()->fromRoute('area-utente/activate-payments'));
        }

        // if not, continue with index action
        $this->setFormsData($this->customer);
        $editForm = true;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            if (isset($postData['customer'])) {
                $postData['customer']['id'] = $this->userService->getIdentity()->getId();

                //prevent gender editing
                $postData['customer']['gender'] = $this->userService->getIdentity()->getGender();

                // ensure vat is not NULL, but a string
                if (is_null($postData['customer']['vat'])) {
                    $postData['customer']['vat'] = '';
                }

                $editForm = $this->processForm($this->profileForm, $postData);
                $this->typeForm = 'edit-profile';
            } else if (isset($postData['password'])) {
                $postData['id'] = $this->userService->getIdentity()->getId();
                $editForm = $this->processForm($this->passwordForm, $postData);
                $this->typeForm = 'edit-pwd';
            }

            if ($editForm) {
                return $this->redirect()->toRoute('area-utente');
            }
        }

        return new ViewModel([
            'bannerJsonpUrl' => $this->bannerJsonpUrl,
            'customer'     => $this->customer,
            'profileForm'  => $this->profileForm,
            'passwordForm' => $this->passwordForm,
            'showError'    => $this->showError,
            'typeForm'     => $this->typeForm,
        ]);
    }

    private function processForm($form, $data)
    {
        $form->setData($data);
        if ($form->isValid()) {
            $customer = $form->saveData();

            //update the data in the form
            $this->setFormsData($customer);

            //update the data in the view
            $this->customer = $customer;

            $this->flashMessenger()->addSuccessMessage(self::SUCCESS_MESSAGE);

            return true;
        }

        $this->showError = true;

        return false;
    }

    private function setFormsData(Customers $customer)
    {
        $customerData = $this->hydrator->extract($customer);
        $this->profileForm->setData(['customer' => $customerData]);
    }

    public function ratesAction()
    {
        return new ViewModel([
            'customer' => $this->customer
        ]);
    }

    public function ratesConfirmAction()
    {
        $option = $this->params()->fromPost('option');

        $customer = $this->I_customersService->setCustomerReprofilingOption($this->customer, $option);

        return new JsonModel([
            'option' => $option
        ]);
    }

    public function pinAction()
    {
        return new ViewModel();
    }

    public function datiPagamentoAction()
    {
        $customer = $this->userService->getIdentity();
        $activateLink = $this->I_customersService->isFirstTripManualPaymentNeeded($customer);
        $cartasiCompletedFirstPayment = $this->cartasiPaymentsService->customerCompletedFirstPayment($customer);
        $tripPayment = $this->tripPaymentsService->getFirstTripPaymentNotPayedByCustomer($customer);

        return new ViewModel([
            'customer' => $customer,
            'cartasiCompletedFirstPayment' => $cartasiCompletedFirstPayment,
            'activateLink' => $activateLink
        ]);
    }

    public function tripsAction()
    {
        return new ViewModel([
            'trips' => $this->I_tripsService->getTripsByCustomer($this->customer->getId())
        ]);
    }

    public function drivingLicenceAction()
    {
        /** @var DriverLicenseForm $form */
        $form = $this->driverLicenseForm;
        $customerData = $this->hydrator->extract($this->customer);
        $form->setData(['driver' => $customerData]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $customer = $this->userService->getIdentity();
            $postData['driver']['id'] = $customer->getId();
            if (!isset($postData['driver']['driverLicenseCategories'])) {
                $driver = $postData['driver'];
                $driver['driverLicenseCategories'] = [];
                $postData['driver'] = $driver;
            }
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    $this->I_customersService->saveDriverLicense($form->getData());

                    $params = [
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

                    $this->getEventManager()->trigger('driversLicenseEdited', $this, $params);

                    $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');
                }

                return $this->redirect()->toRoute('area-utente/patente');
            } else {
                $this->showError = true;
            }
        }

        return new ViewModel([
            'customer'          => $this->customer,
            'driverLicenseForm' => $form,
            'showError'         => $this->showError,
        ]);
    }

    public function bonusAction()
    {
        return new ViewModel([
            'customer'  => $this->customer,
            'listBonus' => $this->I_customersService->getAllBonus($this->customer)
        ]);
    }

    public function additionalServicesAction()
    {
        $form = $this->promoCodeForm;
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            if ($form->isValid()) {
                try {
                    /** @var PromoCodes $promoCode */
                    $promoCode = $this->promoCodeService->getPromoCode($postData['promocode']['promocode']);

                    $this->I_customersService->addBonusFromPromoCode($this->customer, $promoCode);

                    $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');
                } catch (BonusAssignmentException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');
                }

                return $this->redirect()->toRoute('area-utente/additional-services');
            }
        }

        $bonusPackages = $this->bonusPackagesService->getAvailableBonusPackges();

        return new ViewModel([
            'promoCodeForm' => $form,
            'bonusPackages' => $bonusPackages
        ]);
    }

    public function invoicesListAction()
    {
        $customer = $this->userService->getIdentity();
        $availableDates = $this->invoicesService->getDistinctDatesForCustomerByMonth($customer);

        return new ViewModel(
            ['availableDates' => $availableDates]
        );
    }

    public function rentsAction()
    {
        $customer = $this->userService->getIdentity();
        $availableDates = $this->I_tripsService->getDistinctDatesForCustomerByMonth($customer);

        return new ViewModel(
            ['availableDates' => $availableDates]
        );
    }

    public function activatePaymentsAction()
    {
        $customer = $this->userService->getIdentity();
        $expiryDate = '';

        $isActivated = $this->cartasiContractsService->getCartasiContract($customer) != null;
        $tripPayment = $this->tripPaymentsService->getFirstTripPaymentNotPayedByCustomer($customer);
        if ($tripPayment != null) {
            $expiryDate = $this->tripPaymentsService->getExpiryDate($tripPayment)->format('d-m-Y');
        }

        return new ViewModel([
            'customer' => $customer,
            'expiryDate' => $expiryDate,
            'isActivated' => $isActivated,
            'tripPayment' => $tripPayment
        ]);
    }
}
