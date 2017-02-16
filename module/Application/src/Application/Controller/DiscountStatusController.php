<?php

namespace Application\Controller;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\DiscountStatusService;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class DiscountStatusController extends AbstractRestfulController
{
    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var DiscountStatusService
     */
    private $discountStatusService;

    public function __construct(
        CustomersService $customersService,
        DiscountStatusService $discountStatusService
    ) {
        $this->customersService = $customersService;
        $this->discountStatusService = $discountStatusService;
    }

    public function get($email)
    {
        $customer = $this->customersService->findOneByEmail($email);

        if (!$customer instanceof Customers) {
            return $this->response->setStatusCode(400);
        }

        return new JsonModel([
            'status' => $customer->discountStatusValue()
        ]);
    }

    public function create()
    {
        $email = $this->params()->fromPost('email');
        $status = $this->params()->fromPost('status');

        if (empty($email) || empty($status)) {
            return $this->response->setStatusCode(400);
        }

        $customer = $this->customersService->findOneByEmail($email);

        if (!$customer instanceof Customers) {
            return $this->response->setStatusCode(400);
        }

        try {
            $this->discountStatusService->upsertStatus($customer, $status);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500);
        }

        return $this->response->setStatusCode(200);
    }

    public function getList()
    {
        parent::getList();

        return $this->response;
    }

    public function onDispatch(MvcEvent $e)
    {
        if (!$this->requestFromServer($this->request)) {
            return $this->response->setStatusCode(403);
        }

        $method = strtolower($this->request->getMethod());

        if (!in_array($method, ['get', 'post'])) {
            return $this->response->setStatusCode(405);
        }

        return parent::onDispatch($e);
    }
}
