<?php

namespace Application\Listener;

use SharengoCore\Service\EmailService;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\EventInterface;

final class DriversLicensePostValidationNotifier implements SharedListenerAggregateInterface
{
    /**
     * @var EmailService $emailService
     */
    private $emailService;

    public function __construct(
        EmailService $emailService
    ) {
        $this->emailService = $emailService;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'MvLabsDriversLicenseValidation\Job\ValidationJob',
            'validDriversLicense',
            [$this, 'validDriversLicense'],
            -100 // low priority so that sending the mail is the last thing we do
        );

        $this->listeners[] = $events->attach(
            'MvLabsDriversLicenseValidation\Job\ValidationJob',
            'unvalidDriversLicense',
            [$this, 'unvalidDriversLicense'],
            - 100 // low priority so that sending the mail is the last thing we do
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

        $content = file_get_contents(__DIR__.'/../../../view/emails/drivers-license-valid.html');

        $this->emailService->sendEmail(
            $args['email'],
            'RIABILITAZIONE PROFILO',
            $content
        );
    }

    public function unvalidDriversLicense(EventInterface $e)
    {
        $args = $e->getParam('args');

        $content = file_get_contents(__DIR__.'/../../../view/emails/drivers-license-unvalid.html');

        $this->emailService->sendEmail(
            $args['email'],
            'DISABILITAZIONE PROFILO',
            $content
        );
    }
}
