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

class IndexController extends AbstractActionController
{

    /**
     * @var string
     */
    private $mobileUrl;

    public function __construct($mobileUrl)
    {
        $this->mobileUrl = $mobileUrl;
    }

    public function indexAction()
    {
        // Any mobile device (phones or tablets).
        if ($this->mobileDetect()->isMobile()) {
            $this->redirect()->toUrl($this->mobileUrl);
        }

        return new ViewModel();
    }
    
    public function carsharingAction()
    {
        $view = new ViewModel();
        $view->setTemplate('application/index/index');

        return $view;
    }

    public function cosaeAction()
    {
        return new ViewModel();
    }

    public function quantocostaAction()
    {
        return new ViewModel();
    }

    public function comefunzionaAction()
    {
        return new ViewModel();
    }

    public function faqAction()
    {
        return new ViewModel();
    }

    public function contattiAction()
    {
        return new ViewModel();
    }

    public function cookiesAction()
    {
        return new ViewModel();
    }

    public function notelegaliAction()
    {
        return new ViewModel();
    }

    public function privacyAction()
    {
        return new ViewModel();
    }

    public function callcenterAction()
    {
        return new ViewModel();
    }
}
