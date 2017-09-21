<?php

namespace Application\Service;

use SharengoCore\Entity\Customers;
use SharengoCore\Entity\CustomersBonus;
use SharengoCore\Entity\PromoCodes;
use SharengoCore\Entity\PromoCodesInfo;
use SharengoCore\Entity\PromoCodesOnce;

use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\EmailService;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\PromoCodesOnceService;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\Mail\Message;
use Zend\Mime;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\View\HelperPluginManager;
use Zend\EventManager\EventManager;
use Zend\Session\Container;

final class RegistrationService
{
    /**
     * @var Form
     */
    private $form1;

    /**
     * @var Form
     */
    private $form2;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var AbstractHydrator
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
     * @var Translator
     */
    private $translator;

    /**
     * @var HelperPluginManager
     */
    private $viewHelperManager;

    /**
     * @var \SharengoCore\Entity\Repository\CustomersRepository
     */
    private $customersRepository;

    /**
     * @var PromoCodesService;
     */
    private $promoCodesService;

    /**
     * @var PromoCodesOnceService;
     */
    private $promoCodesOnceService;

    /**
     * @var array
     */
    private $subscriptionBonus;

    /**
     * @var CustomerDeactivationService;
     */
    private $deactivationService;

    /**
     * @var EventManager
     */
    private $events;

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
     * @param PromoCodesOnceService $promoCodesOnceService
     * @param array $subscriptionBonus
     * @param EventManager
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
        PromoCodesOnceService $promoCodesOnceService,
        array $subscriptionBonus,
        CustomerDeactivationService $deactivationService,
        EventManager $events
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
        $this->promoCodesOnceService = $promoCodesOnceService;
        $this->subscriptionBonus = $subscriptionBonus;
        $this->customersRepository = $this->entityManager->getRepository('\SharengoCore\Entity\Customers');
        $this->deactivationService = $deactivationService;
        $this->events = $events;
    }

    /**
     * returns an array with the data of the user, or null if the data expired
     *
     * @return array|null
     */
    public function retrieveValidData()
    {
        $dataForm1 = $this->form1->getRegisteredData();
        $dataForm2 = $this->form2->getRegisteredData();
        $promoCode = $this->form1->getRegisteredDataPromoCode();

        if (is_null($dataForm1) || is_null($dataForm2)) {
            return null;
        }
        $userData = $dataForm1->toArray($this->hydrator);
        $driverData = $dataForm2->toArray($this->hydrator);
        $smsVerification=new Container('smsVerification');
        // we compile manually some fields just for the sake of validation
        $userData['smsCode']=$smsVerification->offsetGet('code') ;
        $userData['email2'] = $userData['email'];
        $userData['password2'] = $userData['password'];
        $userData['birthDate'] = $userData['birthDate']->format('d-m-Y');
        $driverData['driverLicenseReleaseDate'] = $driverData['driverLicenseReleaseDate']->format('d-m-Y');
        $driverData['driverLicenseExpire'] = $driverData['driverLicenseExpire']->format('d-m-Y');

        $this->form1->setData([
            'user' => $userData,
            'promocode' => $promoCode,
        ]);
        $this->form2->setData([
            'driver' => $driverData
        ]);

        if (!$this->form1->isValid() || !$this->form2->isValid()) {
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
        $data['taxCode'] = strtoupper(trim($data['taxCode']));
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

            // has customer used a promo code?
            $promoCode = $data['promoCode'];
            $promoCodeOnce = NULL;

            if ('' != $promoCode) {
                $promoCode = $this->promoCodesService->getPromoCode($promoCode);

                if (is_null($promoCode)) { // is a promocode once
                    $this->entityManager->persist($customer);
                    $promoCodeOnce = $this->promoCodesOnceService->usePromoCode($customer, $data['promoCode']);

                    if(!is_null($promoCodeOnce)){
                        $promoCodeInfo = $promoCodeOnce->getPromoCodesInfo();
                        $customer->setDiscountRate($promoCodeInfo->discountPercentage());
                    } else { // error in set promocode once
                        throw new Exception('Promocode once '.$data['promoCode'].' not found,');
                    }
                } else { // is a promocode standard
                    $customerBonus = CustomersBonus::createFromPromoCode($promoCode);
                    $customerBonus->setCustomer($customer);
                    $this->entityManager->persist($customerBonus);

                    // promo codes has a discount percentage
                    if ($promoCode->discountPercentage() > 0) {
                        $discountPercentage = max(
                            $customer->getDiscountRate(),
                            $promoCode->discountPercentage()
                        );
                        $customer->setDiscountRate($discountPercentage);
                    }
                }
            }

            if(is_null($promoCodeOnce)){
                if (!($promoCode instanceof PromoCodes && $promoCode->noStandardBonus())) {
                    // add 100 min bonus
                    $total = $this->subscriptionBonus['total'];
                    /* 1 euro subscription CSD-1204 */
                    $now = new \DateTime();
                    $start = new \DateTime('2017-09-16 00:00:00');
                    $end = new \DateTime('2017-09-22 23:59:59');
                    if ($now >= $start && $now <= $end){
                        $total = 15;
                    }
                    $bonus100mins = CustomersBonus::createBonus(
                        $customer,
                        $total, //$this->subscriptionBonus['total'],
                        $this->subscriptionBonus['description'],
                        $this->subscriptionBonus['valid-to']
                    );
                    $this->entityManager->persist($bonus100mins);
                }
            }


            $this->entityManager->persist($customer);

            $this->deactivationService->deactivateAtRegistration($customer);

            $this->events->trigger('registeredCustomerPersisted', $this, ['customer' => $customer]);

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
     * @param string $language
     */
    public function sendEmail($email, $name, $surname, $hash, $language)
    {
        /** @var callable $url */
        $url = $this->viewHelperManager->get('url');
        /** @var callable $serverUrl */
        $serverUrl = $this->viewHelperManager->get('serverUrl');

        $writeTo = $this->emailSettings['from'];
        $mail = $this->emailService->getMail(1, $language);
        $content = sprintf(
            $mail->getContent(),
            $name,
            $surname,
            $serverUrl().$url('signup_insert').'?user='.$hash//,
            //$writeTo
        );

        $attachments = [];

        $this->emailService->sendEmail(
            $email,
            $mail->getSubject(), //'Conferma la tua iscrizione a Share’nGo',
            $content,
            $attachments
        );

        $this->emailService->sendEmail(
            $this->emailSettings['sharengoNotices'],
            $mail->getSubject(),//'Conferma la tua iscrizione a Share’nGo',
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

    /**
     * Sanitize the mobile number from double zero and create the corret string with "+ dial_code mobile"
     * @param array $data
     * @return array $data
     */
    public function sanitizeDialMobile($data){
        $smsVerification = new Container('smsVerification');

        $dp1 = "+";
        $dp2 = "00";
        $prefix = $smsVerification->offsetGet('dialCode');
        $str1 = $data['mobile'];
        $str1 = preg_replace('/^' . preg_quote($dp1 . $prefix, '/') . '/', '', $str1);
        $str1 = preg_replace('/^' . preg_quote($dp2 . $prefix, '/') . '/', '', $str1);
        $data['mobile'] = '+' . $smsVerification->offsetGet('dialCode') . $str1;

        return $data;
    }

}
