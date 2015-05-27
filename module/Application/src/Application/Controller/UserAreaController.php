<?php

namespace Application\Controller;

use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use SharengoCore\Service\CustomersService;
use SharengoCore\Entity\Customers;

class UserAreaController extends AbstractActionController
{
    const SUCCESS_MESSAGE = 'Operazione eseguita con successo!';

    /**
     * @var CustomersService
     */
    private $I_customersService;

    /*
    * @var \Zend\Authentication\AuthenticationService
    */
    private $userService;

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
     * @var
     */
    private $typeForm;

    /**
     * @var
     */
    private $showError = false;


    public function __construct(
        CustomersService $I_customersService,
        AuthenticationService $userService,
        Form $profileForm,
        Form $passwordForm,
        HydratorInterface $hydrator
    ) {
        $this->I_customersService = $I_customersService;
        $this->userService = $userService;
        $this->customer = $userService->getIdentity();
        $this->profileForm = $profileForm;
        $this->passwordForm = $passwordForm;
        $this->hydrator = $hydrator;
    }

    public function indexAction()
    {
        $this->setFormsData($this->customer);
        $editForm = true;

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();


            if (isset($postData['customer'])) {
                $postData['customer']['id'] = $this->userService->getIdentity()->getId();
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
            'customer'     => $this->customer,
            'profileForm'  => $this->profileForm,
            'passwordForm' => $this->passwordForm,
            'showError'    => $this->showError,
            'typeForm'     => $this->typeForm
        ]);
    }

    private function processForm($form, $data)
    {
        $form->setData($data);
        if ($form->isValid()) {

            $customer = $form->saveData();

            //update the identity in session
            $this->userService->getStorage()->write($customer);

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

        //update the identity in session
        $this->userService->getStorage()->write($customer);

        return new JsonModel([
            'option' => $option
        ]);
    }

    public function datiPagamentoAction()
    {
        return new ViewModel([
            'customer' => $this->customer,
        ]);
    }
}
