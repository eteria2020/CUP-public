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
use Zend\EventManager\EventInterface;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        /*$em = $e->getApplication()->getServiceManager()->get('Doctrine\ORM\EntityManager');
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('gender', 'string');
        $platform->registerDoctrineTypeMapping('_text', 'string');

        $config = $em->getConnection()->getConfiguration();
        //$config->setFilterSchemaAssetsExpression('/^(countries)$/');*/

        $eventManager->getSharedManager()->attach(
            'Application\Controller\PaymentController',
            'successfulPayment',
            function (EventInterface $e) use ($serviceManager) {
                $params = $e->getParams();

                $customer = $params['customer'];

                // send confirmation email
                $paymentService = $serviceManager->get('PaymentService');
                $paymentService->sendCompletionEmail($customer);

                // store discount rate
                $profilingPlatformService = $serviceManager->get('ProfilingPlatformService');
                $customerService = $serviceManager->get('SharengoCore\Service\CustomersService');

                try {

                    $discount = $profilingPlatformService->getDiscountByEmail($customer->getEmail());
                    $customerService->setCustomerDiscountRate($customer, $discount);

                } catch (ProfilingPlatformException $ex) { }
                
            }
        );
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
