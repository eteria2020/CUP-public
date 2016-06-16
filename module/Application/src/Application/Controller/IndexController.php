<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use SharengoCore\Service\ZonesService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    /**
     * @var string
     */
    private $mobileUrl;

    /**
     * @var ZoneService
     */
    private $zoneService;

    /**
     * @param string $mobileUrl
     */
    public function __construct($mobileUrl, ZonesService $zoneService)
    {
        $this->mobileUrl = $mobileUrl;
        $this->zoneService = $zoneService;
    }

    public function indexAction()
    {
        // Any mobile device (phones or tablets).
        /*if ($this->mobileDetect()->isMobile()) {
            $this->redirect()->toUrl($this->mobileUrl);
        }*/

        return new ViewModel();
    }
    
    /**
     * @return \Zend\Http\Response (JSON Format)
     */
    public function getListZonesAction()
    {
        $data = $this->zoneService->getListZones(false, true);

        /** @var array $zone */
        foreach ($data as $zone) {
            $data[$zone['id']] = json_decode($zone['areaUse']);
        }

        $this->getResponse()->setContent(json_encode($data));
        return $this->getResponse();
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
        $this->redirect()->toUrl('http://support.sharengo.it');
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
}
