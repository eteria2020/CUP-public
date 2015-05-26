<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\Application;

class IsUserArea extends AbstractHelper
{
    public function __invoke($route)
    {
        return (strpos($route, 'area-utente') === 0);
    }
}
