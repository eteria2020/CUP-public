<?php

namespace Application\Controller;

use Application\Form\ForeignDriversLicenseForm;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\AuthorityService;
use SharengoCore\Service\ForeignDriversLicenseService;
use SharengoCore\Entity\Customers;
use SharengoCore\Exception\InvalidAuthorityCodeException;
use SharengoCore\Form\DTO\UploadedFile;

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

    /**
     * @var ForeignDriversLicenseService $foreignDriversLicenseService
     */
    private $foreignDriversLicenseService;

    public function __construct(
        ForeignDriversLicenseForm $form,
        CustomersService $customersService,
        AuthorityService $authorityService,
        ForeignDriversLicenseService $foreignDriversLicenseService
    ) {
        $this->form = $form;
        $this->customersService = $customersService;
        $this->authorityService = $authorityService;
        $this->foreignDriversLicenseService = $foreignDriversLicenseService;
    }

    public function foreignDriversLicenseAction()
    {
        $hash = $this->params('hash');

        if (!empty($hash)) {
            $customer = $this->customersService->getUserFromHash($hash);
        } else {
            $customer = $this->identity();
        }

        if (!$customer instanceof Customers) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->isPost()) {
            $this->handlePostRequest($this->getRequest(), $customer);
        }

        $viewData = $this->prepareViewData($customer);

        $viewModel = new ViewModel($viewData);
        $viewModel->setTemplate('partials/foreign-drivers-license-form');

        return $viewModel;
    }

    private function handlePostRequest(Request $request, Customers $customer)
    {
        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $this->form->setData($post);
        if ($this->form->isValid()) {
            try {
                $data = $this->form->getData();
                $uploadedFile = new UploadedFile(
                    $data['drivers-license-file']['name'],
                    $data['drivers-license-file']['type'],
                    $data['drivers-license-file']['tmp_name'],
                    $data['drivers-license-file']['size']
                );
                $this->foreignDriversLicenseService->saveUploadedForeignDriversLicense(
                    $uploadedFile,
                    $customer
                );

                return $this->redirect()->toRoute('foreign-drivers-license-completion');
            } catch (\Exception $e) {
                //TODO
            }
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

    public function completionAction()
    {
        $viewModel = new ViewModel();

        $viewModel->setTemplate('partials/foreign-drivers-license-completion');

        return $viewModel;
    }
}
