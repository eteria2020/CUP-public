<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use SharengoCore\Service\CustomersService;

class PaymentController extends AbstractActionController
{
    /**
     * @var \SharengoCore\Service\CustomersService
     */
    private $I_customersService;

    /**
     * @var type \SpeckPaypal\Service\Request
     */
    private $I_paypalRequest;

    /**
     * @var array
     */
    private $I_paypalConfig;

    /**
     * @var array
     */
    private $I_sharengoConfig;

    /**
     * @param CustomersService $I_customersService
     * @param \SpeckPaypal\Service\Request $I_paypalRequest
     * @param array $I_paypalConfig
     * @param array $I_sharengoConfig
     */
    public function __construct(
        CustomersService $I_customersService,
        \SpeckPaypal\Service\Request $I_paypalRequest,
        array $I_paypalConfig,
        array $I_sharengoConfig
    ) {
        $this->I_customersService = $I_customersService;
        $this->I_paypalRequest = $I_paypalRequest;
        $this->I_paypalConfig = $I_paypalConfig;
        $this->I_sharengoConfig = $I_sharengoConfig;
    }

    public function payAction() {

        $email = urldecode($this->params('email'));

        $customers = $this->I_customersService->findByEmail($email);
        $customer = $customers[0];

        if (null != $customer) {

            $session = new Container('sharengo_user_first_payment');
            $session->id = $customer->getId();

            $s_expresscheckoutEndpoint = $this->I_paypalConfig['expresscheckout_endpoint'];

            $I_paymentDetails = new \SpeckPaypal\Element\PaymentDetails([
                'amt' => $this->I_sharengoConfig['card-cost'],
                'paymentAction' => 'Sale',
                'currencyCode' => 'EUR'
            ]);

            $I_express = new \SpeckPaypal\Request\SetExpressCheckout(['paymentDetails' => $I_paymentDetails]);
            $I_express->setReturnUrl($this->url()->fromRoute('pay-return', [], ['force_canonical' => true]));
            $I_express->setCancelUrl($this->url()->fromRoute('pay-error', [], ['force_canonical' => true]));

            $I_response = $this->I_paypalRequest->send($I_express);

            $s_token = $I_response->getToken();
            $s_payerId = $I_response->getPayerId();

            if ($I_response->isSuccess()) {
                // Redirect to the PayPal express checkout page, using the token.
                return $this->redirect()->toUrl($s_expresscheckoutEndpoint .'?&cmd=_express-checkout&token=' . $s_token);

            } else {
                return $this->redirect()->toRoute('pay-error');
            }

        } else {
            return $this->redirect()->toRoute('pay-error');
        }
    }

    public function payReturnAction(){

        $session = new Container('sharengo_user_first_payment');
        if (null != $session->id) {

            $customer = $this->I_customersService->findById($session->id);

            if (null != $customer) {

                $I_request = $this->getRequest();
                $s_token   = $I_request->getQuery()->get('token');
                $s_payerId = $I_request->getQuery()->get('PayerID');

                $I_paymentDetails = new \SpeckPaypal\Element\PaymentDetails([
                    'amt' => $this->I_sharengoConfig['card-cost'],
                    'paymentAction' => 'Sale',
                    'currencyCode' => 'EUR'
                ]);

                //To capture express payment
                $I_captureExpress = new \SpeckPaypal\Request\DoExpressCheckoutPayment([
                    'token'             => $s_token,
                    'payerId'           => $s_payerId,
                    'paymentDetails'    => $I_paymentDetails
                ]);

                $I_response = $this->I_paypalRequest->send($I_captureExpress);

                if ($I_response->isSuccess()){

                    $this->I_customersService->confirmFirstPaymentCompleted($customer);

                    $eventManager = $this->getEventManager();
                    $eventManager->trigger('successfulPayment', $this, [
                        'customer' => $customer
                    ]);

                    unset($session->id);

                    return $this->redirect()->toRoute('pay-success');
                }
            }
        }

        return $this->redirect()->toRoute('pay-error');
    }

    public function payErrorAction() {
        return new ViewModel();
    }

    public function paySuccessAction() {
        return new ViewModel();
    }
}
