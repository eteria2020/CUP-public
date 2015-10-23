<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LandingPageController extends AbstractActionController
{

    public function eqSharingAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function bikemiAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function teatroElfoAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function firenzeAction()
    {
        return (new viewModel())->setTerminal(true);
    }

    public function linearAction()
    {
        return (new viewModel())->setTerminal(true);
    }

}
