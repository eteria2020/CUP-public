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

    /**
     * @var aray $emailSettings
     */

    public function __construct(
        EmailService $emailService,
        array $emailSettings
    ) {
        $this->emailService = $emailService;
        $this->emailSettings = $emailSettings;
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

        $mail = $this->emailService->getMail(2, $args['language']);

        $this->emailService->sendEmail(
            $args['email'],
            $mail->getSubject(),//'VALIDAZIONE PATENTE',
            $mail->getContent()
        );
    }

    public function unvalidDriversLicense(EventInterface $e)
    {
        $args = $e->getParam('args');

        $mail = $this->emailService->getMail(4, $args['language']);
        $content = $mail->getContent();
        $subject = $mail->getSubject();//'Share\'ngo - Disabilitazione profilo';

        $this->emailService->sendEmail($args['email'], $subject, $content);
    }
}
