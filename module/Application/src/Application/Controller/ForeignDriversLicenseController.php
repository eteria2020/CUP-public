<?php

namespace Application\Controller;

use Application\Form\ForeignDriversLicenseForm;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\AuthorityService;
use SharengoCore\Entity\Customers;
use SharengoCore\Exception\InvalidAuthorityCodeException;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;

class ForeignDriversLicenseController extends AbstractActionController
{
    /**
     * @var ForeignDriversLicenseForm $form
     */
    private $form;

    /**
     * @var CustomersService $customersService
     */
    private $customersService;

    /**
     * @var AuthorityService $authorityService
     */
    private $authorityService;

    public function __construct(
        ForeignDriversLicenseForm $form,
        CustomersService $customersService,
        AuthorityService $authorityService
    ) {
        $this->form = $form;
        $this->customersService = $customersService;
        $this->authorityService = $authorityService;
    }

    public function foreignDriversLicenseAction()
    {
        $customerId = $this->params('customerId');

        $customer = $this->customersService->findById($customerId);

        if (!$customer instanceof Customers) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->isPost()) {
            $this->handlePostRequest($this->getRequest());
        }

        $viewData = $this->prepareViewData($customer);

        $viewModel = new ViewModel($viewData);
        $viewModel->setTemplate('partials/foreign-drivers-license-form');

        return $viewModel;
    }

    private function handlePostRequest(Request $request)
    {
        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $this->form->setData($post);
        if ($this->form->isValid()) {
            $data = $this->form->getData();

            //return $this->redirect()->toRoute();
        }
    }

    private function prepareViewData(Customers $customer)
    {
        try {
            $authority = $this->authorityService->getByCode($customer->getDriverLicenseAuthority())->getName();
        } catch (InvalidAuthorityCodeException $e) {
            $authority = $customer->getDriverLicenseAuthority();
        }

        $categories = str_replace(',', ' e ', trim($customer->getDriverLicenseCategories(), '{}'));

        return [
            'form' => $this->form,
            'customer' => $customer,
            'authority' => $authority,
            'categories' => $categories
        ];
    }
}
