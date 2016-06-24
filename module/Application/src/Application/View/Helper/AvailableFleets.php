<?php

namespace Application\View\Helper;

use SharengoCore\Service\FleetService;
use SharengoCore\Exception\FleetNotFoundException;
use SharengoCore\Entity\Fleet;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Request;

class AvailableFleets extends AbstractHelper
{
    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var FleetService
     */
    protected $fleetService;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param AuthenticationService $authenticationService
     * @param FleetService $fleetService
     * @param Request $request
     */
    public function __construct(
        AuthenticationService $authenticationService,
        FleetService $fleetService,
        Request $request
    ) {
        $this->authenticationService = $authenticationService;
        $this->fleetService = $fleetService;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        $currentFleet = $this->currentFleet();
        $fleets = $this->fleetService->getAllFleets();


        $ret = sprintf(
            '<div class="block-languages block-menu">
                <ul>
                    <li>
                        <a class="js-show-element" data-longitude="%s" data-latitude="%s">
                            <span>%s</span>
                            <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="js-collapse-box block-available-languages hidden">',
            $currentFleet->getLongitude(),
            $currentFleet->getLatitude(),
            $currentFleet->getName()
        );

        foreach ($fleets as $fleet) {
            $ret .= sprintf(
                '<li>
                    <a href="javascript:void(0);" data-longitude="%s" data-latitude="%s" data-name="%s" data-id="%s">%3$s</a>
                </li>',
                $fleet->getLongitude(),
                $fleet->getLatitude(),
                $fleet->getName(),
                $fleet->getId()
            );
        }

        $ret .= '</ul></li></ul></div>';

        return $ret;
    }

    /**
     * @return Fleet
     */
    public function currentFleet()
    {
        $cookieFleet = $this->retrieveFleetFromCookies();

        if ($cookieFleet instanceof Fleet) {
            return $cookieFleet;
        }

        $customer = $this->authenticationService->getIdentity();

        return $this->fleetService->getCustomerOrDefaultFleet($customer);
    }

    /**
     * @return Fleet|null
     */
    public function retrieveFleetFromCookies()
    {
        if (!isset($this->request->getCookie()->sharengo_map_fleetPreference)) {
            return null;
        }

        $cookieFleetId = $this->request->getCookie()->sharengo_map_fleetPreference;

        if (!is_numeric($cookieFleetId)) {
            return null;
        }

        try {
            return $this->fleetService->getFleetById($cookieFleetId);
        } catch (FleetNotFoundException $e) {
            return null;
        }
    }
}
