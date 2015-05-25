<?php

namespace Multilanguage;

use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;
use Zend\Http\Request as HttpRequest;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $serviceManager = $e->getApplication()->getServiceManager();
        $languageService = $serviceManager->get('LanguageService');

        //set translator to be used when translating form erroe messages
        AbstractValidator::setDefaultTranslator($languageService->getTranslator());

        if ($e->getRequest() instanceof HttpRequest) {

            // before the routing happens we assign to the router a translator so
            // that we can translate urls
            $eventManager->attach(
                MvcEvent::EVENT_ROUTE,
                function (MvcEvent $e) use ($languageService) {
                    // first thing we need to set the correct locale
                    $languageService->setLocaleFromRequest($e->getRequest());

                    // next we give to the router the translator to use to perform
                    // the url translation
                    $e->getRouter()->setTranslator($languageService->getTranslator(), 'routes');
                },
                100
            );

            // rendering the page we set the language in the layout and view
            // viewmodels
            $eventManager->attach(
                MvcEvent::EVENT_RENDER,
                function (MvcEvent $e) use ($languageService) {
                    $layout = $viewModel = $e->getViewModel();

                    if ($layout->getChildren()) {

                        $view = $layout->getChildren()[0]; //WARNING: this will work only until our view is the first child of the layout

                        $lang = $languageService->getLanguage();

                        $layout->lang = $lang;
                        $view->lang = $lang;
                    }
                }
            );
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

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
}
