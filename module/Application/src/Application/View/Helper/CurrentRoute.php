<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\Application;

class CurrentRoute extends AbstractHelper
{
    /**
     * the application that performs the routing
     * @var Zend\Mvc\Application
     */
    private $application;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function __invoke()
    {
        $routeMatch = $this->application->getMvcEvent()->getRouteMatch();

        // if there is no route match (i.e. a 404) we return null
        if ($routeMatch) {
            return $routeMatch->getMatchedRouteName();
        }
    }
}
