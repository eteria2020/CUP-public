<?php

namespace Application\Service;

use SharengoCore\Entity\Customers;
use SharengoCore\Entity\CustomersBonus;
use SharengoCore\Service\PromoCodesService;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime;
use Zend\Mvc\I18n\Translator;
use Zend\View\HelperPluginManager;
use Doctrine\ORM\EntityManager;

final class RegistrationService
{
    /**
     * @var \Zend\Form\Form
     */
    private $form1;

    /**
     * @var \Zend\Form\Form
     */
    private $form2;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Zend\Stdlib\Hydrator\AbstractHydrator
     */
    private $hydrator;

    /**
     * @var \Zend\Mail\Transport\TransportInterface
     */
    private $emailTransport;

    /**
     * @var array
     */
    private $emailSettings;

    /**
     * @var \Zend\Mvc\I18n\Translator
     */
    private $translator;

    /**
     * @var \Zend\View\HelperPluginManager
     */
    private $viewHelperManager;

    /**
     *
     * @var \SharengoCore\Entity\Repository\CustomersRepository
     */
    private $customersRepository;

    /**
     *
     * @var type \SharengoCore\Service\PromoCodesService;
     */
    private $promoCodesService;

    public function __construct(
        Form $form1,
        Form $form2,
        EntityManager $entityManager,
        AbstractHydrator $hydrator,
        TransportInterface $emailTransport,
        array $emailSettings,
        Translator $translator,
        HelperPluginManager $viewHelperManager,
        PromoCodesService $promoCodesService
    ) {
        $this->form1 = $form1;
        $this->form2 = $form2;
        $this->entityManager = $entityManager;
        $this->hydrator = $hydrator;
        $this->emailTransport = $emailTransport;
        $this->emailSettings = $emailSettings;
        $this->translator = $translator;
        $this->viewHelperManager = $viewHelperManager;
        $this->promoCodesService = $promoCodesService;

        $this->customersRepository = $this->entityManager->getRepository('\SharengoCore\Entity\Customers');
    }

    /**
     * returns an array with the data of the user, or null if the data expired
     *
     * @return array|null
     */
    public function retrieveData()
    {
        $dataForm1 = $this->form1->getRegisteredData();
        $dataForm2 = $this->form2->getRegisteredData();
        $promoCode = $this->form1->getRegisteredDataPromoCode();

        if (empty($dataForm1) || empty($dataForm2)) {
            return null;
        } else {
            $dataForm1 = $this->hydrator->extract($dataForm1);
            $dataForm2 = $this->hydrator->extract($dataForm2);
        }

        $data = [];

        foreach ($dataForm1 as $key => $value) {
            if (is_null($value)) {
                $data[$key] = $dataForm2[$key];
            } else {
                $data[$key] = $dataForm1[$key];
            }
        }

        if ('' != $promoCode) {
            $data['promoCode'] = $promoCode['promocode'];
        }

        return $data;
    }

    public function formatData($data)
    {
        $data['driverLicenseCategories'] = '{' .implode(',', $data['driverLicenseCategories']). '}';
        $data['password'] = hash("MD5", $data['password']);
        $data['hash'] = hash("MD5", strtoupper($data['email']).strtoupper($data['password']));
        $data['profilingCounter'] = (int) $data['profilingCounter'];

        return $data;
    }

    public function notifySharengoByMail($data)
    {
        $mail = (new Message())
            ->setFrom($this->emailSettings['from'])
            ->setTo($this->emailSettings['sharengoNotices'])
            ->setSubject("NUOVA REGISTRAZIONE DA SITO")
            ->setReplyTo($this->emailSettings['replyTo'])
            ->setBody(json_encode($data))
            ->setEncoding("UTF-8");
        $mail->getHeaders()->addHeaderLine('X-Mailer', $this->emailSettings['X-Mailer']);

        $this->emailTransport->send($mail);
    }

    public function notifySharengoErrorByEmail($message)
    {
        $mail = (new Message())
            ->setFrom($this->emailSettings['from'])
            ->setTo($this->emailSettings['sharengoNotices'])
            ->setSubject("ERRORE NUOVA REGISTRAZIONE DA SITO")
            ->setReplyTo($this->emailSettings['replyTo'])
            ->setBody($message)
            ->setEncoding("UTF-8");
        $mail->getHeaders()->addHeaderLine('X-Mailer', $this->emailSettings['X-Mailer']);

        $this->emailTransport->send($mail);
    }

    public function saveData($data)
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $customer = new Customers();
            $customer = $this->hydrator->hydrate($data, $customer);

            //generate primary PIN
            $primary = mt_rand(1000,9999);
            $pins = ['primary' => $primary];

            $customer->setPin(json_encode($pins));

            $this->entityManager->persist($customer);

            // add 100 min bonus
            $bonus100mins = new \SharengoCore\Entity\CustomersBonus();
            $bonus100mins->setCustomer($customer);
            $bonus100mins->setInsertTs($customer->getInsertedTs());
            $bonus100mins->setUpdateTs($bonus100mins->getInsertTs());
            $bonus100mins->setTotal(100);
            $bonus100mins->setResidual(100);
            $bonus100mins->setValidFrom($bonus100mins->getInsertTs());
            $defaultBonusExpiryDate = \DateTime::createFromFormat('Y-m-d H:i:s', '2015-12-31 23:59:59');
            $bonus100mins->setValidTo($defaultBonusExpiryDate);
            $bonus100mins->setDescription('Bonus iscrizione utente');
            $this->entityManager->persist($bonus100mins);

            // has customer used a promo code?
            $promoCode = $data['promoCode'];
            if ('' != $promoCode) {
                $customerBonus = CustomersBonus::createFromPromoCode($this->promoCodesService->getPromoCode($promoCode));
                $customerBonus->setCustomer($customer);
                
                $this->entityManager->persist($customerBonus);
            }

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function sendEmail($email, $name, $surname, $hash)
    {
        $url = $this->viewHelperManager->get('url');
        $serverUrl = $this->viewHelperManager->get('serverUrl');

        $content = sprintf(
            file_get_contents(__DIR__.'/../../../view/emails/registration-' . $this->translator->getLocale() . '.html'),
            $name,
            $surname,
            $serverUrl().$url('signup_insert').'?user='.$hash
        );

        $text = new Mime\Part($content);
        $text->type = Mime\Mime::TYPE_HTML;
        $text->charset = 'utf-8';

        $image1 = file_get_contents(__DIR__.'/../../../../../public/images/bannerphono.jpg');
        $attachment1 = new Mime\Part($image1);
        $attachment1->type = Mime\Mime::TYPE_OCTETSTREAM;
        $attachment1->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
        $attachment1->encoding = Mime\Mime::ENCODING_BASE64;
        $attachment1->filename = 'bannerphono.jpg';
        $attachment1->id = 'bannerphono.jpg';

        $image2 = file_get_contents(__DIR__.'/../../../../../public/images/barbarabacci.jpg');
        $attachment2 = new Mime\Part($image2);
        $attachment2->type = Mime\Mime::TYPE_OCTETSTREAM;
        $attachment2->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
        $attachment2->encoding = Mime\Mime::ENCODING_BASE64;
        $attachment2->filename = 'barbarabacci.jpg';
        $attachment2->id = 'barbarabacci.jpg';

        $mimeMessage = new Mime\Message();
        $mimeMessage->setParts([$text, $attachment1, $attachment2]);

        $mail = (new Message())
            ->setFrom($this->emailSettings['from'])
            ->setTo(strtolower($email))
            ->setSubject("SHARENGO: CONFERMA REGISTRAZIONE E ATTIVAZIONE")
            ->setReplyTo($this->emailSettings['replyTo'])
            ->setBcc($this->emailSettings['registrationBcc'])
            ->setBody($mimeMessage)
            ->setEncoding("UTF-8");
        $mail->getHeaders()->addHeaderLine('X-Mailer', $this->emailSettings['X-Mailer']);

        $this->emailTransport->send($mail);

        $mail = (new Message())
            ->setFrom($this->emailSettings['from'])
            ->setTo($this->emailSettings['sharengoNotices'])
            ->setSubject("MAIL NUOVA REGISTRAZIONE DA SITO")
            ->setReplyTo($this->emailSettings['replyTo'])
            ->setBody($mimeMessage)
            ->setEncoding("UTF-8");
        $mail->getHeaders()->addHeaderLine('X-Mailer', $this->emailSettings['X-Mailer']);

        $this->emailTransport->send($mail);
    }

    public function removeSessionData()
    {
        $this->form1->clearRegisteredData();
        $this->form2->clearRegisteredData();
    }

    public function getUserFromHash($hash) {
        return $this->customersRepository->findOneBy([
            'hash' => $hash
        ]);
    }
    
    public function registerUser($hash)
    {
        
        $customer = $this->customersRepository->findBy([
            'hash' => $hash
        ]);

        if (empty($customer)) {
            $message = $this->translator->translate('PREREGISTRAZIONE SCADUTA');
        } else {
            $customer = $customer[0];
            if ($customer->getRegistrationCompleted()) {
                $message = $this->translator->translate("UTENTE GIA' REGISTRATO");
            } else {
                $this->entityManager->getConnection()->beginTransaction();

                try {
                    $customer->setRegistrationCompleted(true);

                    $this->entityManager->persist($customer);
                    $this->entityManager->flush();
                    $this->entityManager->getConnection()->commit();

                    $message = $this->translator->translate("UTENTE REGISTRATO CON SUCCESSO");
                } catch (\Exception $e) {
                    $this->entityManager->getConnection()->rollback();
                    $message = $this->translator->translate("SI &Egrave; VERIFICATO UN PROBLEMA DURANTE LA REGISTRAZIONE");
                }
            }
        }

        return $message;
    }
}
