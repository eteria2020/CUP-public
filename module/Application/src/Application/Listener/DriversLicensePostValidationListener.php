<?php

namespace Application\Listener;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\EventInterface;

final class DriversLicensePostValidationListener implements SharedListenerAggregateInterface
{
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'MvLabsDriversLicenseValidation\Job\ValidationJob',
            'validDriversLicense',
            [$this, 'validDriversLicense']
        );

        $this->listeners[] = $events->attach(
            'MvLabsDriversLicenseValidation\Job\ValidationJob',
            'unvalidDriversLicense',
            [$this, 'unvalidDriversLicense']
        );
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function validDriversLicense(EventInterface $e)
    {
        $args = $e->getParam('args');
        $response = $e->getParam('response');

        $line = $this->csvLine($args, $response);

        $this->writeToCsv($line);

        /*$message = "Driver's license valid with data " .
            json_encode($args) .
            " and response " .
            json_encode((array) $response) .
            "\n";

        file_put_contents($file, $message, FILE_APPEND);*/
    }

    public function unvalidDriversLicense(EventInterface $e)
    {
        $args = $e->getParam('args');
        $response = $e->getParam('response');

        $line = $this->csvLine($args, $response);

        $this->writeToCsv($line);

        /*$message = "Driver's license not valid with data " .
            json_encode($args) .
            " and response " .
            json_encode((array) $response) .
            "\n";

        file_put_contents($file, $message, FILE_APPEND);*/
    }

    private function csvLine($args, $response)
    {
        return [
            $args['email'],
            $args['name'],
            $args['surname'],
            $args['driverLicense'],
            $args['taxCode'],
            $args['birthDate']['date'],
            $args['birthCountry'],
            $args['birthProvince'],
            $args['birthTown'],
            $response->valid(),
            $response->code(),
            $response->message()
        ];
    }

    private function writeToCsv($line)
    {
        $file = __DIR__ . '/../../../../../data/log/driversLicense.log';

        $fp = fopen($file, 'a');
        fputcsv($fp, $line);

        fclose($fp);
    }
}
