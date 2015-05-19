<?php

namespace Application\Service;

use Zend\Mail\Transport\TransportInterface;
use Zend\Mvc\I18n\Translator;
use Zend\Mime;
use Zend\Mail\Message;

final class PaymentService
{
    /**
     * @var TransportInterface
     */
    private $emailTransport;

    /**
     * @var array
     */
    private $emailSettings;

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(
        TransportInterface $emailTransport,
        array $emailSettings,
        Translator $translator
    ) {
        $this->emailTransport = $emailTransport;
        $this->emailSettings = $emailSettings;
        $this->translator = $translator;
    }

    public function sendCompletionEmail($customer)
    {
        $content = sprintf(
            file_get_contents(__DIR__.'/../../../view/emails/payment-confirmation-' . $this->translator->getLocale() . '.html'),
            $customer->getName(),
            $customer->getSurname()/*,
            $customer->getPin()*/
        );

        $text = new Mime\Part($content);
        $text->type = Mime\Mime::TYPE_HTML;
        $text->charset = 'utf-8';

        $image1 = file_get_contents(__DIR__.'/../../../../../public/images/banneremail.png');
        $attachment1 = new Mime\Part($image1);
        $attachment1->type = Mime\Mime::TYPE_OCTETSTREAM;
        $attachment1->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
        $attachment1->encoding = Mime\Mime::ENCODING_BASE64;
        $attachment1->filename = 'banneremail.png';
        $attachment1->id = 'banneremail.png';

        $image2 = file_get_contents(__DIR__.'/../../../../../public/images/barbarabacci.png');
        $attachment2 = new Mime\Part($image2);
        $attachment2->type = Mime\Mime::TYPE_OCTETSTREAM;
        $attachment2->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
        $attachment2->encoding = Mime\Mime::ENCODING_BASE64;
        $attachment2->filename = 'barbarabacci.png';
        $attachment2->id = 'barbarabacci.png';

        /*$fileContent1 = file_get_contents(__DIR__.'/../../../../../public/pdf/Contratto_Sharengo.pdf');
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
        $attachment3->encoding = Mime\Mime::ENCODING_BASE64;*/

        $mimeMessage = new Mime\Message();
        $mimeMessage->setParts([$text, $attachment1, $attachment2]);

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
