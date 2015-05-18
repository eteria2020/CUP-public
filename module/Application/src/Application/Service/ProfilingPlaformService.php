<?php

namespace Application\Service;

use Application\Exception\ProfilingPlatformException;

use Zend\Http\Client;

final class ProfilingPlaformService
{

    /**
     *
     * @var array
     */
    private $profilingPlatformSettings;
    
    public function __construct(array $profilingPlatformSettings) {
        $this->profilingPlatformSettings = $profilingPlatformSettings;
    }
    
    public function getDiscountByEmail($email) {

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

}