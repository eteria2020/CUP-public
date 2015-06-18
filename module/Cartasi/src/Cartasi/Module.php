<?php

namespace Cartasi;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\Mail\Message;
use Zend\Mime;
use Zend\Mail\Transport\Sendmail;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Returns autoloader configuration
     * @return multitype:multitype:multitype:string
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();

        $serviceManager = $e->getApplication()->getServiceManager();
        $alertSettings = $serviceManager->get('Configuration')['alertSettings'];

        $eventManager->getSharedManager()->attach(
            'Cartasi\Controller\CartasiPaymentsController',
            [
                'cartasi.first_payment.invalid_mac',
                'cartasi.first_payment.wrong_transaction',
                'cartasi.first_payment.wrong_transaction_data',
                'cartasi.first_payment.wrong_contract',
                'cartasi.first_payment.update_error',
                'cartasi.first_payment.return_update_error',
                'cartasi.recurring_payment.wrong_data',
                'cartasi.recurring_payment.update_error',
                'cartasi.recurring_payment.invalid_contract_number',
                'cartasi.recurring_payment.invalid_contract'
            ],
            function (EventInterface $e) use ($alertSettings) {
                $this->sendAlertEmail($e, $alertSettings);
            }
        );
    }

    /**
     * @var EventInterface $e
     * @var array $settings
     */
    private function sendAlertEmail(EventInterface $e, array $settings)
    {
        $emailTransport = new Sendmail();

        $content = $e->getName().PHP_EOL.json_encode($e->getParams());

        $text = new Mime\Part($content);
        $text->type = Mime\Mime::TYPE_TEXT;
        $text->charset = 'utf-8';

        $mimeMessage = new Mime\Message();
        $mimeMessage->setParts([$text]);

        $mail = (new Message())
            ->setFrom($settings['from'])
            ->setTo($settings['to'])
            ->setSubject("SHARENGO: ERRORE")
            ->setBody($mimeMessage)
            ->setEncoding("UTF-8");

        $emailTransport->send($mail);
    }
}
