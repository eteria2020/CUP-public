<?php

namespace Application\Service;

use Zend\Mail\Transport\TransportInterface;

final class PaymentService
{
    /**
     * @var TransportInterface
     */
    private $emailTransport;

    private $emailSettings;

    public function __construct(TransportInterface $emailTransport, array $emailSettings)
    {
        $this->emailTransport = $emailTransport;
        $this->emailSettings = $emailSettings;
    }

    public function sendCompletionEmail($customer)
    {
        $content = sprintf(
            file_get_contents(__DIR__.'/../../../view/emails/payment-confirmation-' . $this->translator->getLocale() . '.txt'),
            $customer->getName(),
            $customer->getPin()
        );

        $text = new Mime\Part($content);
        $text->type = Mime\Mime::TYPE_TEXT;
        $text->charset = 'utf-8';

        $fileContent1 = file_get_contents(__DIR__.'/../../../../../public/pdf/Contratto_Sharengo.pdf');
        $attachment1 = new Mime\Part($fileContent1);
        $attachment1->type = 'application/pdf';
        $attachment1->filename = 'Contratto_Sharengo.pdf';
        $attachment1->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
        $attachment1->encoding = Mime\Mime::ENCODING_BASE64;

        $fileContent2 = file_get_contents(__DIR__.'/../../../../../public/pdf/Informativa_Privacy.pdf');
        $attachment2 = new Mime\Part($fileContent2);
        $attachment2->type = 'application/pdf';
        $attachment2->filename = 'Informativa_Privacy.pdf';
        $attachment2->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
        $attachment2->encoding = Mime\Mime::ENCODING_BASE64;

        $fileContent3 = file_get_contents(__DIR__.'/../../../../../public/pdf/Regolamento_Sharengo.pdf');
        $attachment3 = new Mime\Part($fileContent3);
        $attachment3->type = 'application/pdf';
        $attachment3->filename = 'Regolamento_Sharengo.pdf';
        $attachment3->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
        $attachment3->encoding = Mime\Mime::ENCODING_BASE64;

        $mimeMessage = new Mime\Message();
        $mimeMessage->setParts([$text, $attachment1, $attachment2, $attachment3]);

        $mail = (new Message())
            ->setFrom($this->emailSettings['from'])
            ->setTo(strtolower($customer->getEmail()))
            ->setSubject("SHARENGO: CONFERMA PAGAMENTO")
            ->setReplyTo($this->emailSettings['replyTo'])
            ->setBcc($this->emailSettings['registrationBcc'])
            ->setBody($mimeMessage)
            ->setEncoding("UTF-8");
        $mail->getHeaders()->addHeaderLine('X-Mailer', $this->emailSettings['X-Mailer']);

        $this->emailTransport->send($mail);
    }
}
