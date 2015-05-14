<?php

namespace Application\Service;

use SharengoCore\Entity\Customers;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
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

    public function __construct(
        Form $form1,
        Form $form2,
        EntityManager $entityManager,
        AbstractHydrator $hydrator,
        TransportInterface $emailTransport,
        array $emailSettings,
        Translator $translator,
        HelperPluginManager $viewHelperManager
    ) {
        $this->form1 = $form1;
        $this->form2 = $form2;
        $this->entityManager = $entityManager;
        $this->hydrator = $hydrator;
        $this->emailTransport = $emailTransport;
        $this->emailSettings = $emailSettings;
        $this->translator = $translator;
        $this->viewHelperManager = $viewHelperManager;

        $this->customersRepository = $this->entityManager->getRepository('\SharengoCore\Entity\Customers');
    }

    public function retrieveData()
    {
        $dataForm1 = $this->form1->getRegisteredData()->toArray(); //use hydration
        $dataForm2 = $this->form2->getRegisteredData()->toArray(); //use hydration

        $data = [];

        foreach ($dataForm1 as $key => $value) {
            if (is_null($value)) {
                $data[$key] = $dataForm2[$key];
            } else {
                $data[$key] = $dataForm1[$key];
            }
        }

        return $data;
    }

    public function formatData($data)
    {
        $data['driverLicenseCategories'] = '{' .$data['driverLicenseCategories']. '}';
        $data['password'] = hash("MD5", $data['password']);
        $data['hash'] = hash("MD5", strtoupper($data['email']).strtoupper($data['password']));

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

            $this->entityManager->persist($customer);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function sendEmail($email, $surname, $hash)
    {
        $url = $this->viewHelperManager->get('url');
        $serverUrl = $this->viewHelperManager->get('serverUrl');

        $message = sprintf(
            file_get_contents(__DIR__.'/../../../view/emails/registration-' . $this->translator->getLocale() . '.txt'),
            $surname,
            $serverUrl().$url('signup_insert').'?user='.$hash
        );

        $mail = (new Message())
            ->setFrom($this->emailSettings['from'])
            ->setTo(strtolower($email))
            ->setSubject("SHARENGO: CONFERMA REGISTRAZIONE E ATTIVAZIONE")
            ->setReplyTo($this->emailSettings['replyTo'])
            ->setBcc($this->emailSettings['registrationBcc'])
            ->setBody($message)
            ->setEncoding("UTF-8");
        $mail->getHeaders()->addHeaderLine('X-Mailer', $this->emailSettings['X-Mailer']);

        $this->emailTransport->send($mail);

        $mail = (new Message())
            ->setFrom($this->emailSettings['from'])
            ->setTo($this->emailSettings['sharengoNotices'])
            ->setSubject("MAIL NUOVA REGISTRAZIONE DA SITO")
            ->setReplyTo($this->emailSettings['replyTo'])
            ->setBody($message)
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
