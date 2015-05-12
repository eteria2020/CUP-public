<?php

namespace Application\Service;

//use TwistCore\Entity\Repository\ClientiTempRepository;
//use TwistCore\Entity\ClientiTemp;
use TwistCore\Entity\Customer;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mvc\I18n\Translator;
use Zend\Mvc\Service\ViewHelperManager;
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
     * @var array
     */
    private $websiteSettings;

    /**
     * @var \Zend\Mvc\I18n\Translator
     */
    private $translator;

    /**
     * @var \Zend\Mvc\Service\ViewHelperManager
     */
    private $viewHelperManager;

    public function __construct(
        Form $form1,
        Form $form2,
        EntityManager $entityManager,
        AbstractHydrator $hydrator,
        TransportInterface $emailTransport,
        array $emailSettings,
        array $websiteSettings,
        Translator $translator,
        ViewHelperManager $viewHelperManager
    ) {
        $this->form1 = $form1;
        $this->form2 = $form2;
        $this->entityManager = $entityManager;
        $this->hydrator = $hydrator;
        $this->emailTransport = $emailTransport;
        $this->emailSettings = $emailSettings;
        $this->websiteSettings = $websiteSettings;
        $this->translator = $translator;
        $this->viewHelperManager = $viewHelperManager;
    }

    public function retrieveData()
    {
        $dataForm1 = $this->form1->getRegisteredData()->toArray();
        $dataForm2 = $this->form2->getRegisteredData()->toArray();

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
        /*if (is_array($data['patTipo'])) {
            $data['patTipo'] = implode(' ', $data['patTipo']);
        }
        $data['hash'] = hash("MD5", strtoupper($data['email']).strtoupper($data['password']));
        $data['inserito'] = $this->microNow();
        $data['email'] = strtoupper($data['email']);
        $data['cf'] = strtoupper($data['cf']);
        $data['patNumero'] = strtoupper($data['patNumero']);*/

        return $data;
    }

    /*private function microNow()
    {
        $micro  = microtime();
        $time   = explode(" ", $micro);
        $now    = explode(".", $time[0]);
        $now1   = substr($now[1], 0);   // isola la parte decimale
        $now2   = substr($now1, 0, 6);  // tiene solo 3 decimali

        $date   = date("Y-m-d H:i:s.").$now2;
        return $date;
    }*/

    public function notifySharengoByMail($data)
    {
        $mail = (new Message())
            ->setFrom($this->emailSettings['from'])
            ->setTo($this->emailSettings['twistNotices'])
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
            ->setTo($this->emailSettings['twistNotices'])
            ->setSubject("ERRORE NUOVA REGISTRAZIONE DA SITO")
            ->setReplyTo($this->emailSettings['replyTo'])
            ->setBody($message)
            ->setEncoding("UTF-8");
        $mail->getHeaders()->addHeaderLine('X-Mailer', $this->emailSettings['X-Mailer']);

        $this->emailTransport->send($mail);
    }

    public function saveData($data)
    {
        /*$clientiTempRepository = $this->entityManager->getRepository('\TwistCore\Entity\ClientiTemp');
        $alreadyCliente = $clientiTempRepository->findBy([
            'email' => $data['email']
        ]);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            if ($alreadyCliente) {
                $this->entityManager->remove($alreadyCliente[0]);
            }

            $clienteTemp = new ClientiTemp();
            $clienteTemp = $this->hydrator->hydrate($data, $clienteTemp);

            $this->entityManager->persist($clienteTemp);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }*/
    }

    public function sendEmail($email, $surname, $hash)
    {
        /*$url = $this->viewHelperManager->get('url');

        $message = sprintf(
            file_get_contents(__DIR__.'/../../../view/emails/registration-' . $this->translator->getLocale() . '.txt'),
            $surname,
            'https://'.$this->websiteSettings['hostname'].$url('index/signup_insert').'?user='.$hash,
            $this->websiteSettings['hostname'],
            $this->emailSettings['replyTo']
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
            ->setTo($this->emailSettings['twistNotices'])
            ->setSubject("MAIL NUOVA REGISTRAZIONE DA SITO")
            ->setReplyTo($this->emailSettings['replyTo'])
            ->setBody($message)
            ->setEncoding("UTF-8");
        $mail->getHeaders()->addHeaderLine('X-Mailer', $this->emailSettings['X-Mailer']);

        $this->emailTransport->send($mail);*/
    }

    public function removeSessionData()
    {
        $this->form1->clearRegisteredData();
        $this->form2->clearRegisteredData();
    }

    public function registerUser($hash)
    {
        /*$clientiTemp = $this->entityManager->getRepository('\TwistCore\Entity\ClientiTemp');
        $customer = $this->entityManager->getRepository('\TwistCore\Entity\Customer');

        $clienteTemp = $clientiTemp->findBy([
            'hash' => $hash
        ]);

        if (empty($clienteTemp)) {
            $message = $this->translator->translate('PREREGISTRAZIONE SCADUTA');
        } else {
            $clienteTemp = $clienteTemp[0];
            $customer = $customer->findByEmailOrTaxCode(
                $clienteTemp->getEmail(),
                $clienteTemp->getCf()
            );

            if (!empty($customer)) {
                $message = $this->translator->translate("UTENTE GIA' REGISTRATO");
            } else {
                $this->entityManager->getConnection()->beginTransaction();

                try {
                    $this->entityManager->remove($clienteTemp);

                    $customer = new Customer();
                    $data = $this->createCustomerFromTemp($clienteTemp);
                    $customer = $this->hydrator->hydrate($data, $customer);

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

        return $message;*/
    }

    /*private function createCustomerFromTemp(ClientiTemp $clienteTemp)
    {
        return [
            'email' => $clienteTemp->getEmail(),
            'pin' => $clienteTemp->getPin(),
            'sesso' => $clienteTemp->getSesso(),
            'nome' => $clienteTemp->getNome(),
            'cognome' => $clienteTemp->getCognome(),
            'dataDiNascita' => $clienteTemp->getDataNascita(),
            'cittaNascita' => $clienteTemp->getCittaNascita(),
            'statoNascita' => $clienteTemp->getStatoNascita(),
            'via' => $clienteTemp->getResIndirizzo(),
            'info' => $clienteTemp->getResInfo(),
            'cap' => $clienteTemp->getResCap(),
            'citta' => $clienteTemp->getResCitta(),
            'lingua' => $clienteTemp->getLingua(),
            'cf' => $clienteTemp->getCf(),
            'piva' => $clienteTemp->getPiva(),
            'cellulare' => $clienteTemp->getCellulare(),
            'telefono' => $clienteTemp->getTelefono(),
            'studente' => $clienteTemp->getStudente(),
            'matricola' => $clienteTemp->getMatricola(),
            'universita' => $clienteTemp->getUniversita(),
            'patente' => $clienteTemp->getPatNumero(),
            'patenteUfficio' => $clienteTemp->getPatUfficio(),
            'patenteRilascio' => $clienteTemp->getPatRilascio(),
            'patenteNome' => $clienteTemp->getPatNome(),
            'patenteNazione' => $clienteTemp->getPatNazione(),
            'scadenzaPatente' => $clienteTemp->getPatScadenza(),
            'tipoPatente' => $clienteTemp->getPatTipo(),
            'timeInserimento' => $this->microNow(),
            'password' => $clienteTemp->getPassword()
        ];
    }*/
}
