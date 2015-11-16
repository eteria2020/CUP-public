<?php

namespace Application\Service;

use SharengoCore\Entity\Customers;
use SharengoCore\Entity\CustomersBonus;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\EmailService;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\Mail\Message;
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
     * @var array
     */
    private $emailSettings;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var \Zend\Mvc\I18n\Translator
     */
    private $translator;

    /**
     * @var \Zend\View\HelperPluginManager
     */
    private $viewHelperManager;

    /**
     * @var \SharengoCore\Entity\Repository\CustomersRepository
     */
    private $customersRepository;

    /**
     * @var \SharengoCore\Service\PromoCodesService;
     */
    private $promoCodesService;

    /**
     * @var array
     */
    private $subscriptionBonus;

    /**
     * @param Form $form1
     * @param Form $form2
     * @param EntityManager $entityManager
     * @param AbstractHydrator $hydrator
     * @param array $emailSettings
     * @param EmailService $emailService
     * @param Translator $translator
     * @param HelperPluginManager $viewHelperManager
     * @param PromoCodesService $promoCodesService
     * @param array $subscriptionBonus
     */
    public function __construct(
        Form $form1,
        Form $form2,
        EntityManager $entityManager,
        AbstractHydrator $hydrator,
        array $emailSettings,
        EmailService $emailService,
        Translator $translator,
        HelperPluginManager $viewHelperManager,
        PromoCodesService $promoCodesService,
        array $subscriptionBonus
    ) {
        $this->form1 = $form1;
        $this->form2 = $form2;
        $this->entityManager = $entityManager;
        $this->hydrator = $hydrator;
        $this->emailSettings = $emailSettings;
        $this->emailService = $emailService;
        $this->translator = $translator;
        $this->viewHelperManager = $viewHelperManager;
        $this->promoCodesService = $promoCodesService;
        $this->subscriptionBonus = $subscriptionBonus;
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
        // we need to pass from the entity to the id
        $data['fleet'] = $data['fleet']->getId();

        $data['email'] = strtolower($data['email']);

        // ensure the vat is not NULL, but a string
        if (is_null($data['vat'])) {
            $data['vat'] = '';
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function formatData($data)
    {
        $data['driverLicenseCategories'] = '{' .implode(',', $data['driverLicenseCategories']). '}';
        $data['password'] = hash("MD5", $data['password']);
        $data['hash'] = hash("MD5", strtoupper($data['email']).strtoupper($data['password']));
        $data['profilingCounter'] = (int) $data['profilingCounter'];

        return $data;
    }

    /**
     * @param array $data
     */
    public function notifySharengoByMail($data)
    {
        $this->emailService->sendEmail(
            $this->emailSettings['sharengoNotices'],
            'NUOVA REGISTRAZIONE DA SITO',
            json_encode($data)
        );
    }

    /**
     * @param string $message
     */
    public function notifySharengoErrorByEmail($message)
    {
        $this->emailService->sendEmail(
            $this->emailSettings['sharengoNotices'],
            'ERRORE NUOVA REGISTRAZIONE DA SITO',
            $message
        );
    }

    /**
     * @param array $data
     */
    public function saveData($data)
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $customer = new Customers();
            $customer = $this->hydrator->hydrate($data, $customer);

            //generate primary PIN
            $primary = mt_rand(1000, 9999);
            $pins = ['primary' => $primary];

            $customer->setPin(json_encode($pins));

            $this->entityManager->persist($customer);

            // add 100 min bonus
            $bonus100mins = CustomersBonus::createBonus(
                $customer,
                $this->subscriptionBonus['total'],
                $this->subscriptionBonus['description'],
                $this->subscriptionBonus['valid-to']
            );
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

    /**
     * @param string $email
     * @param string $name
     * @param string $surname
     * @param string $hash
     */
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

        $attachments = [
            'bannerphono.jpg' => __DIR__.'/../../../../../public/images/bannerphono.jpg',
            'barbarabacci.jpg' => __DIR__.'/../../../../../public/images/barbarabacci.jpg'
        ];

        $this->emailService->sendEmail(
            $email,
            'Conferma la tua iscrizione a Share’nGo',
            $content,
            $attachments
        );

        $this->emailService->sendEmail(
            $this->emailSettings['sharengoNotices'],
            'Conferma la tua iscrizione a Share’nGo',
            $content,
            $attachments
        );
    }

    public function removeSessionData()
    {
        $this->form1->clearRegisteredData();
        $this->form2->clearRegisteredData();
    }

    /**
     * @param string $hash
     * @return Customers
     */
    public function getUserFromHash($hash)
    {
        return $this->customersRepository->findOneBy([
            'hash' => $hash
        ]);
    }

    /**
     * @param string $hash
     * @return string
     */
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
