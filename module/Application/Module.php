<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use BjyAuthorize\View\RedirectionStrategy;
use Zend\EventManager\EventInterface;

use Application\Exception\ProfilingPlatformException;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $userService = $serviceManager->get('zfcuser_auth_service');

        /*$em = $e->getApplication()->getServiceManager()->get('Doctrine\ORM\EntityManager');
        $config = $em->getConnection()->getConfiguration();
        $config->setFilterSchemaAssetsExpression('/^(transactions)$/');*/

        $eventManager->getSharedManager()->attach(
            ['Application\Controller\PaymentController', 'Cartasi\Controller\CartasiPaymentsController'],
            'successfulPayment',
            function (EventInterface $e) use ($serviceManager) {
                $params = $e->getParams();

                $customer = $params['customer'];

                // send confirmation email
                $paymentService = $serviceManager->get('PaymentService');
                $paymentService->sendCompletionEmail($customer);

                // enable api usage
                $customerService = $serviceManager->get('SharengoCore\Service\CustomersService');
                $customerService->enableApi($customer);
                
            }
        );

        $eventManager->getSharedManager()->attach(
            'Application\Controller\UserController',
            'registrationCompleted',
            function (EventInterface $e) use ($serviceManager) {
                $params = $e->getParams();

                // store discount rate
                $profilingPlatformService = $serviceManager->get('ProfilingPlatformService');
                $customerService = $serviceManager->get('SharengoCore\Service\CustomersService');

                $customer = $customerService->findByEmail($params['email']);

                if (empty($customer)) {
                    return;
                } else {
                    $customer = $customer[0];
                }

                // retrieve discout from equomobili
                try {
                    $discount = $profilingPlatformService->getDiscountByEmail($params['email']);
                    $customerService->setCustomerDiscountRate($customer, $discount);
                } catch (ProfilingPlatformException $ex) { }

                // assign card to user
                $customerService->assignCard($customer);
            }
        );

        // BjyAuthorize redirection strategy
        $strategy = new RedirectionStrategy();
        $eventManager->attach($strategy);

        $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
        $viewModel->loggedUser = $userService->getIdentity();
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function successfulPayment(EventInterface $e)
    {
        $params = $e->getParams();

        $serviceManager = $e->getApplication()->getServiceManager();
        $paymentservice->sendCompletionEmail($params['customer']);
    }
}
