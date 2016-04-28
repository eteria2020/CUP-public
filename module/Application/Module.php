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
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $userService = $serviceManager->get('zfcuser_auth_service');

        $successfulPaymentListener = $serviceManager->get('Application\Listener\SuccessfulPaymentListener');
        $eventManager->getSharedManager()->attachAggregate($successfulPaymentListener);

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
                } catch (ProfilingPlatformException $ex) {
                }

                // assign card to user
                $customerService->assignCard($customer);
            }
        );

        // attach driver's license validation listener
        $driversLicenseValidationListener = $serviceManager->get('Application\Listener\DriversLicenseValidationListener');
        $eventManager->getSharedManager()->attachAggregate($driversLicenseValidationListener);

        // attach listener to react on driver's license validation
        $driversLicensePostValidationLogger = $serviceManager->get('Application\Listener\DriversLicensePostValidationLogger');
        $eventManager->getSharedManager()->attachAggregate($driversLicensePostValidationLogger);

        $driversLicensePostValidation = $serviceManager->get('Application\Listener\DriversLicensePostValidationListener');
        $eventManager->getSharedManager()->attachAggregate($driversLicensePostValidation);

        $driversLicensePostValidationNotifier = $serviceManager->get('Application\Listener\DriversLicensePostValidationNotifier');
        $eventManager->getSharedManager()->attachAggregate($driversLicensePostValidationNotifier);

        $driversLicenseEditing = $serviceManager->get('Application\Listener\DriversLicenseEditingListener');
        $eventManager->getSharedManager()->attachAggregate($driversLicenseEditing);

        $uploadedDriversLicenseMailSender = $serviceManager->get('SharengoCore\Listener\UploadedDriversLicenseMailSender');
        $eventManager->getSharedManager()->attachAggregate($uploadedDriversLicenseMailSender);

        $providerAuthenticatedCustomerRegisterd = $serviceManager->get('Application\Listener\ProviderAuthenticatedCustomerRegistered');
        $eventManager->getSharedManager()->attachAggregate($providerAuthenticatedCustomerRegisterd);

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

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * @param EventInterface $e
     */
    public function successfulPayment(EventInterface $e)
    {
        $params = $e->getParams();

        $serviceManager = $e->getApplication()->getServiceManager();
        $paymentservice->sendCompletionEmail($params['customer']);
    }
}
