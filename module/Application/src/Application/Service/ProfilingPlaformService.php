<?php

namespace Application\Service;

use Application\Exception\ProfilingPlatformException;
use SharengoCore\Service\FleetService;

use Zend\Http\Client;

final class ProfilingPlaformService
{
    /**
     * @var array
     */
    private $profilingPlatformSettings;

    /**
     * @var FleetService
     */
    private $fleetService;

    /**
     * @param array $profilingPlatformSettings
     * @param FleetService $fleetService
     */
    public function __construct(
        array $profilingPlatformSettings,
        FleetService $fleetService
    ) {
        $this->profilingPlatformSettings = $profilingPlatformSettings;
        $this->fleetService = $fleetService;
    }

    /**
     * @param string $email
     */
    public function getDiscountByEmail($email)
    {
        $client = new Client();

        $uri = $this->profilingPlatformSettings['endpoint'] . $this->profilingPlatformSettings['getdiscount-call'];

        $client->setUri(sprintf($uri, $email));

        $response = $client->send();

        switch($response->getStatusCode()) {
            case 200:
                $body = $response->getBody();
                $data = json_decode($body, true);
                if ($data['status']) {
                    return $data['data'];
                }
                throw new ProfilingPlatformException('Response error');
            case 404:
                throw new ProfilingPlatformException('User not found');
            default:
                throw new ProfilingPlatformException('Generic response error');
        }
    }

    /**
     * @param string $email
     */
    public function getPromoCodeByEmail($email)
    {
        $client = new Client();

        $uri = $this->profilingPlatformSettings['endpoint'] . $this->profilingPlatformSettings['getpromocode-call'];

        $client->setUri(sprintf($uri, $email));

        $response = $client->send();

        switch($response->getStatusCode()) {
            case 200:
                $body = $response->getBody();
                $data = json_decode($body, true);
                if ($data['status'] && null != $data['data']) {
                    return $data['data'];
                }
                throw new ProfilingPlatformException('Response error');
            case 404:
                throw new ProfilingPlatformException('User not found');
            default:
                throw new ProfilingPlatformException('Generic response error');
        }
    }

    /**
     * @param string $email
     */
    public function getFleetByEmail($email)
    {
        $client = new Client();

        $uri = $this->profilingPlatformSettings['endpoint'] . $this->profilingPlatformSettings['getfleet-call'];

        $client->setUri(sprintf($uri, $email));

        $response = $client->send();

        switch ($response->getStatusCode()) {
            case 200:
                $body = $response->getBody();
                $data = json_decode($body, true);
                if ($data['status'] && !is_null($data['data'])) {
                    $fleetId = $data['data'];
                    return $this->fleetService->getFleetById($fleetId);
                }
                throw new ProfilingPlatformException('Response error');
            case 404:
                throw new ProfilingPlatformException('User not found');
            default:
                throw new ProfilingPlatformException('Generic response error');
        }
    }
}
