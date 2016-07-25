<?php

namespace Application\Controller;

use SharengoCore\Entity\CustomersBonus;
use SharengoCore\Entity\PromoCodes;
use SharengoCore\Service\TripsService;
use Application\Form\DriverLicenseForm;

use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\EventManager\EventManager;
use Zend\Stdlib\Parameters;

use SharengoCore\Service\CustomersService;
use SharengoCore\Entity\Customers;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Entity\Invoices;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Exception\BonusAssignmentException;

use Cartasi\Service\CartasiPaymentsService;
use Cartasi\Service\CartasiContractsService;

class UserAreaController extends AbstractActionController
{
    const SUCCESS_MESSAGE = 'Operazione eseguita con successo!';

    /**
     * @var CustomersService
     */
    private $customerService;

    /**
     * @var TripsService
     */
    private $tripsService;

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
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @var CartasiContractsService
     */
    private $cartasiContractsService;

    /**
     * @var string
     */
    private $bannerJsonpUrl;

    /**
     * @var string
     */
    private $discounterUrl;

    /**
     * @param CustomersService $customerService
     * @param TripsService $tripsService
     * @param AuthenticationService $userService
     * @param InvoicesService $invoicesService
     * @param Form $profileForm
     * @param Form $passwordForm
     * @param Form $driverLicenseForm
     * @param HydratorInterface $hydrator
     * @param CartasiPaymentsService $cartasiPaymentsService
     * @param TripPaymentsService $tripPaymentsService
     * @param CartasiContractsService $cartasiContractsService
     * @param string $bannerJsonpUrl
     * @param string $discounterUrl
     */
    public function __construct(
        CustomersService $customerService,
        TripsService $tripsService,
        AuthenticationService $userService,
        InvoicesService $invoicesService,
        Form $profileForm,
        Form $passwordForm,
        Form $driverLicenseForm,
        HydratorInterface $hydrator,
        CartasiPaymentsService $cartasiPaymentsService,
        TripPaymentsService $tripPaymentsService,
        CartasiContractsService $cartasiContractsService,
        $bannerJsonpUrl,
        $discounterUrl
    ) {
        $this->customerService = $customerService;
        $this->tripsService = $tripsService;
        $this->userService = $userService;
        $this->invoicesService = $invoicesService;
        $this->customer = $userService->getIdentity();
        $this->profileForm = $profileForm;
        $this->passwordForm = $passwordForm;
        $this->driverLicenseForm = $driverLicenseForm;
        $this->hydrator = $hydrator;
        $this->cartasiPaymentsService = $cartasiPaymentsService;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->cartasiContractsService = $cartasiContractsService;
        $this->bannerJsonpUrl = $bannerJsonpUrl;
        $this->discounterUrl = $discounterUrl;
    }

    public function indexAction()
    {
        // check wether the customer still needs to register a credit card
        $customer = $this->userService->getIdentity();
        if ($this->customerService->isFirstTripManualPaymentNeeded($customer)) {
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

                $customerOldTaxCode = $customer->getTaxCode();
                $editForm = $this->processForm($this->profileForm, $postData);
                $this->typeForm = 'edit-profile';

                if ($postData['customer']['taxCode'] != $customerOldTaxCode) {
                    // if we change the tax code we need to revalidate the driver's license
                    $params = [
                        'email' => $postData['customer']['email'],
                        'driverLicense' => $customer->getDriverLicense(),
                        'taxCode' => $postData['customer']['taxCode'],
                        'driverLicenseName' => $customer->getDriverLicenseName(),
                        'driverLicenseSurname' => $customer->getDriverLicenseSurname(),
                        'birthDate' => ['date' => date_create($postData['customer']['birthDate'])->format('Y-m-d')],
                        'birthCountry' => $postData['customer']['birthCountry'],
                        'birthProvince' => $postData['customer']['birthProvince'],
                        'birthTown' => $postData['customer']['birthTown']
                    ];

                    $this->getEventManager()->trigger('taxCodeEdited', $this, $params);
                }
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
        $customer = $this->identity();

        if (!$customer instanceof Customers) {
            return $this->response->setStatusCode(403);
        }

        return new ViewModel([
            'customer' => $this->customer,
            'discounterUrl' => $this->discounterUrl,
            'showNewDiscount' => $customer->deservesNewDiscount()
        ]);
    }

    public function ratesConfirmAction()
    {
        $option = $this->params()->fromPost('option');

        $customer = $this->customerService->setCustomerReprofilingOption($this->customer, $option);

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
        $activateLink = $this->customerService->isFirstTripManualPaymentNeeded($customer);
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
            'trips' => $this->tripsService->getTripsByCustomer($this->customer->getId())
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
                    $this->customerService->saveDriverLicense($form->getData());

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
                    $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. Ci scusiamo per l\'inconveniente');
                }

                return $this->redirect()->toRoute('area-utente/patente');
            } else {
                $this->showError = true;
            }
        }

        $driversLicenseUpload = $this->customerService->customerNeedsToAcceptDriversLicenseForm($this->customer) &&
            !$this->customerService->customerHasAcceptedDriversLicenseForm($this->customer);

        return new ViewModel([
            'customer' => $this->customer,
            'driverLicenseForm' => $form,
            'showError' => $this->showError,
            'driversLicenseUpload' => $driversLicenseUpload
        ]);
    }

    public function bonusAction()
    {
        return new ViewModel([
            'customer'  => $this->customer,
            'listBonus' => $this->customerService->getAllBonus($this->customer)
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
        $availableDates = $this->tripsService->getDistinctDatesForCustomerByMonth($customer);

        return new ViewModel(
            ['availableDates' => $availableDates]
        );
    }

    public function activatePaymentsAction()
    {
        $customer = $this->userService->getIdentity();

        $isActivated = $this->cartasiContractsService->getCartasiContract($customer) != null;
        $tripPayment = $this->tripPaymentsService->getFirstTripPaymentNotPayedByCustomer($customer);

        return new ViewModel([
            'customer' => $customer,
            'isActivated' => $isActivated,
            'tripPayment' => $tripPayment
        ]);
    }
}
