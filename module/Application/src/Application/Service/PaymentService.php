<?php

namespace Application\Service;

use SharengoCore\Service\EmailService;

use Zend\Mvc\I18n\Translator;
use Zend\Mime;
use Zend\Mail\Message;

final class PaymentService
{
    /**
     * @var array
     */
    private $emailSettings;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param array $emailSettings
     * @param EmailService $emailService
     * @param Translator $translator
     */
    public function __construct(
        array $emailSettings,
        EmailService $emailService,
        Translator $translator
    ) {
        $this->emailSettings = $emailSettings;
        $this->emailService = $emailService;
        $this->translator = $translator;
    }

    public function sendCompletionEmail($customer)
    {
        $content = sprintf(
            file_get_contents(__DIR__.'/../../../view/emails/payment-confirmation-' . $this->translator->getLocale() . '.html'),
            $customer->getName(),
            $customer->getSurname()
        );

        $attachments = [
            'bannerphono.jpg' => __DIR__.'/../../../../../public/images/bannerphono.jpg',
            'barbarabacci.jpg' => __DIR__.'/../../../../../public/images/barbarabacci.jpg',
            'like.jpg' => __DIR__.'/../../../../../public/images/like.jpg'
        ];

        $this->emailService->sendEmail(
            $customer->getEmail(),
            'SHARENGO: CONFERMA PAGAMENTO',
            $content,
            $attachments
        );
    }
}
