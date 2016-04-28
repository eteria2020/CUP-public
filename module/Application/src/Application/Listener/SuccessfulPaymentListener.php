<?php

namespace Application\Listener;

use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\CustomersService;
use Application\Service\PaymentService;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\EventInterface;

class SuccessfulPaymentListener implements SharedListenerAggregateInterface
{
    /**
     * @var CustomerDeactivationService
     */
    private $deactivationService;

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var PaymentService
     */
    private $paymentService;

    public function __construct(
        CustomerDeactivationService $deactivationService,
        CustomersService $customersService,
        PaymentService $paymentService
    ) {
        $this->deactivationService = $deactivationService;
        $this->customersService = $customersService;
        $this->paymentService = $paymentService;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'Cartasi\Controller\CartasiPaymentsController',
            'successfulPayment',
            [$this, 'successfulPayment']
        );
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($index, $callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function successfulPayment(EventInterface $e)
    {
        $params = $e->getParams();

        $customer = $params['customer'];

        // send confirmation email
        $this->paymentService->sendCompletionEmail($customer);
        $this->customersService->enableCustomerPayment($customer);

        // enable api usage
        if (!$this->deactivationService->hasActiveDeactivations($customer)) {
            $this->customersService->enableApi($customer);
        }
    }
}
