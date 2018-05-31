<?php

namespace Application\Service;

use SharengoCore\Entity\CustomerDeactivation;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\CustomersBonus;
use SharengoCore\Entity\PromoCodes;
use SharengoCore\Entity\PromoCodesInfo;
use SharengoCore\Entity\PromoCodesOnce;
use CodiceFiscale\Checker;

use SharengoCore\Service\CountriesService;
use SharengoCore\Service\CustomerDeactivationService;
use SharengoCore\Service\EmailService;
use SharengoCore\Service\MunicipalitiesService;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\PromoCodesOnceService;
use SharengoCore\Service\PromoCodesMemberGetMemberService;

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
     * @var Form
     */
    private $newForm;

    /**
     * @var Form
     */
    private $newForm2;

    /**
     * @var Form
     */
    private $optionalForm;

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
     * @var MunicipalitiesService
     */
    private $municipalitiesService;

    /**
     * @var CountriesService
     */
    private $countriesService;

    /**
     * @var PromoCodesMemberGetMemberService;
     */
    private $promoCodesMemberGetMemberService;

    /**
     * @param Form $form1
     * @param Form $form2
     * @param Form $newForm
     * @param Form $newForm2
     * @param EntityManager $entityManager
     * @param AbstractHydrator $hydrator
     * @param array $emailSettings
     * @param EmailService $emailService
     * @param Translator $translator
     * @param HelperPluginManager $viewHelperManager
     * @param PromoCodesService $promoCodesService
     * @param PromoCodesOnceService $promoCodesOnceService
     * @param PromoCodesMemberGetMemberService $promoCodesMemberGetMemberService
     * @param array $subscriptionBonus
     * @param EventManager $events
     * @param MunicipalitiesService $municipalitiesService
     */
    public function __construct(
        Form $form1,
        Form $form2,
        Form $newForm,
        Form $newForm2,
        Form $optionalForm,
        EntityManager $entityManager,
        AbstractHydrator $hydrator,
        array $emailSettings,
        EmailService $emailService,
        Translator $translator,
        HelperPluginManager $viewHelperManager,
        PromoCodesService $promoCodesService,
        PromoCodesOnceService $promoCodesOnceService,
        PromoCodesMemberGetMemberService $promoCodesMemberGetMemberService,
        array $subscriptionBonus,
        CustomerDeactivationService $deactivationService,
        EventManager $events,
        MunicipalitiesService $municipalitiesService,
        CountriesService $countriesService

    ) {
        $this->form1 = $form1;
        $this->form2 = $form2;
        $this->newForm = $newForm;
        $this->newForm2 = $newForm2;
        $this->optionalForm = $optionalForm;
        $this->entityManager = $entityManager;
        $this->hydrator = $hydrator;
        $this->emailSettings = $emailSettings;
        $this->emailService = $emailService;
        $this->translator = $translator;
        $this->viewHelperManager = $viewHelperManager;
        $this->promoCodesService = $promoCodesService;
        $this->promoCodesOnceService = $promoCodesOnceService;
        $this->promoCodesMemberGetMemberService = $promoCodesMemberGetMemberService;
        $this->subscriptionBonus = $subscriptionBonus;
        $this->customersRepository = $this->entityManager->getRepository('\SharengoCore\Entity\Customers');
        $this->deactivationService = $deactivationService;
        $this->events = $events;
        $this->municipalitiesService = $municipalitiesService;
        $this->countriesService = $countriesService;
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
                        throw new \Exception('Promocode once '.$data['promoCode'].' not found,');
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

    /* ===  NEW SIGNUP === */

    public function newRetrieveValidData()
    {
        $dataForm1 = $this->newForm->getRegisteredData();

        if (is_null($dataForm1)) {
            return null;
        }
        $userData = $dataForm1->toArray($this->hydrator);


        $this->newForm->setData([
            'user' => $userData,
        ]);

        if (!$this->newForm->isValid()) {
            return null;
        } else {
            $dataForm1 = $this->hydrator->extract($dataForm1);
        }

        $data = [];

        foreach ($dataForm1 as $key => $value) {
            if (!is_null($value)) {
                $data[$key] = $dataForm1[$key];
            }
        }

        // we need to pass from the entity to the id
        $data['fleet'] = $data['fleet']->getId();

        $data['email'] = strtolower($data['email']);

        // ensure the vat is not NULL, but a string
        if (!isset($data['vat']) || is_null($data['vat'])) {
            $data['vat'] = '';
        }

        return $data;
    }

    public function saveData1($data)
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $customer = new Customers();

            $customer = $this->hydrator->hydrate($data, $customer);

            //generate primary PIN
            $primary = mt_rand(1000, 9999);
            $pins = ['primary' => $primary];

            $customer->setPin(json_encode($pins));
            //$customer->setRegistrationCompleted(true);

            $this->entityManager->persist($customer);

            $this->deactivationService->deactivateAtRegistration($customer);
            $this->deactivationService->deactivateRegistrationNotCompleted($customer);

            $this->events->trigger('registeredCustomerPersisted', $this, ['customer' => $customer]);

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return $customer;
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function formatData1($data)
    {
        //$data['taxCode'] = strtoupper(trim($data['taxCode']));
        //$data['driverLicenseCategories'] = '{' .implode(',', $data['driverLicenseCategories']). '}';
        $data['vat'] = '';
        $data['language'] = 'it';

        /* DRIVER LICENSE TEMP DATA */
        $data['driverLicenseForeign'] = false;
        $data['driverLicenseReleaseDate'] = null;
        $data['driverLicenseExpire'] = null;

        /* GENERAL CONDITION AND PRIVACY */
        $data['generalCondition1'] = 1;
        $data['generalCondition2'] = 1;
        $data['privacyInformation'] = 1;

        /* SET REGISTRATION COMPLETE TRUE */
        //$data['registrationCompleted'] = true;

        $data['password'] = hash("MD5", $data['password']);
        $data['hash'] = hash("MD5", strtoupper($data['email']).strtoupper($data['password']));
        $data['profilingCounter'] = (int) $data['profilingCounter'];
        return $data;
    }

    public function removeSessionData1()
    {
        $this->newForm->clearRegisteredData();
        //$this->newForm->clearRegisteredData();
    }

    /**
     * @param $civico
     * @param $files
     * returns an array with the data of the user, or null if the data expired
     *
     * @return array|null
     */
    public function retrieveValidData2($civico, $files)
    {
        $dataForm2 = $this->newForm2->getRegisteredData();
        $promoCode = $this->newForm2->getRegisteredDataPromoCode();

        if (is_null($dataForm2)) {
            return null;
        }

        $userData = $dataForm2->toArray($this->hydrator);
        $smsVerification=new Container('smsVerification');
        // we compile manually some fields just for the sake of validation
        $userData['smsCode']=$smsVerification->offsetGet('code');
        $userData['driverLicenseReleaseDate'] = null;
        $userData['driverLicenseExpire'] = null;
        $userData['civico'] = $civico;

        $this->newForm2->setData([
            'user1' => $userData,
            'promocode' => $promoCode,
            'signature' => $files['signature'],
            'drivers-license-front' => $files['drivers-license-front'],
            'drivers-license-back' => $files['drivers-license-back'],
            'identity-front' => $files['drivers-license-front'],
            'identity-back' => $files['drivers-license-back'],
        ]);

        if (!$this->newForm2->isValid()) {
            return null;
        } else {
            $dataForm2 = $this->hydrator->extract($dataForm2);
        }

        $data = [];

        foreach ($dataForm2 as $key => $value) {
            if (!is_null($value)) { //!
/*                $data[$key] = $dataForm2[$key];
            } else {*/
                $data[$key] = $dataForm2[$key];
            }
        }

        if ('' != $promoCode) {
            $data['promoCode'] = $promoCode['promocode'];
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function formatData2($data, $civico, Customers $customer)
    {
        $data['id'] = $customer->getId();
        $data['pin'] = $customer->getPin();
        $data['email'] = $customer->getEmail();
        $data['generalCondition1'] = 1;
        $data['generalCondition2'] = 1;
        $data['privacyCondition'] = 1;
        $data['surname'] = ($data['surname'] == null || $data['surname'] == '') ? $data['driverLicenseSurname'] : $data['surname'];
        $data['name'] = ($data['name'] == null || $data['name'] == '') ? $data['driverLicenseName'] : $data['name'];;
        $data['address'] = $data['address'].' '.$civico;
        $data['taxCode'] = strtoupper($data['taxCode']);
        $data['driverLicenseCountry'] = $data['driverLicenseForeign'] == 'true' ? 'ee' : 'it';
        $chk = new Checker();
        if ($chk->isFormallyCorrect($data['taxCode'])){
            $birthYear = $chk->getYearBirth();

            if ($birthYear > (date('y')-18)){
                $birthYear = '19'.$birthYear;
            } else {
                $birthYear = '20'.$birthYear;
            }
            $data['birthDate'] = date_create($chk->getDayBirth().'-'.$chk->getMonthBirth().'-'.$birthYear);
            switch ($chk->getSex()){
                case 'M':
                    $gender = 'male';
                    break;
                case 'F':
                    $gender = 'female';
                    break;
                default:
                    $gender = 'male';
            }
            $data['gender'] = $gender;

            if ($chk->getCountryBirth()[0] != 'Z') { //born in italy
                $municipality = $this->municipalitiesService->getMunicipalityByCadastralCode($chk->getCountryBirth())[0];
                $data['birthProvince'] = $municipality->getProvince();
                $data['birthTown'] = $municipality->getName();
                $data['birthCountry'] = 'it';
            } else {
                //foreign born
                $data['birthProvince'] = 'EE';
                $data['birthTown'] = 'EE';
                $data['birthCountry'] = $this->countriesService->getCountryByCadastralCode($chk->getCountryBirth());
            }

        } else {
            return null;
        }

        return $data;
    }

    public function updateData2($data)
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $customer = new Customers();

            $customer = $this->hydrator->hydrate($data, $customer);

            $customer->setRegistrationCompleted(true);

            $this->entityManager->persist($customer);

            $this->assingPromocode($data['promoCode'], $customer);

            $this->events->trigger('registeredCustomerPersisted', $this, ['customer' => $customer]);

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();

            /* remove REGISTRATION NOT COMPLETED in customer_deactivations */

            $deactivation = $this->deactivationService->getAllActive(
                $customer,
                CustomerDeactivation::REGISTRATION_NOT_COMPLETED
            );

            $this->deactivationService->reactivateForRegistrationCompleted($deactivation);

            return $customer;
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function removeSessionData2()
    {
        $this->newForm2->clearRegisteredData();
    }

    /**
     * returns an array with the data of the user, or null if the data expired
     *
     * @return array|null
     */
    public function retrieveValidOptionalData()
    {
        $optionalForm = $this->optionalForm->getRegisteredData();

        if (is_null($optionalForm)) {
            return null;
        }

        $optionalData = $optionalForm->toArray($this->hydrator);

        $this->optionalForm->setData([
            'optional' => $optionalData,
        ]);

        if (!$this->optionalForm->isValid()) {
            return null;
        } else {
            $optionalForm = $this->hydrator->extract($optionalForm);
        }

        $data = [];

        foreach ($optionalForm as $key => $value) {
            if (!is_null($value)) { //!
                /*$data[$key] = $dataForm2[$key];
                  } else {*/
                $data[$key] = $optionalForm[$key];
            }
        }

        return $data;
    }

    public function updateOptionalData($data, $customerId)
    {
        $customer = $this->customersRepository->findOneById($customerId);
        $customer->setVat($data["vat"]);
        $customer->setNewsletter(($data["newsletter"] == "on"));
        $customer->setHowToKnow($data["howToKnow"]);
        $customer->setJobType($data["jobType"]);

        $this->entityManager->getConnection()->beginTransaction();

        try {

            $this->entityManager->persist($customer);

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            $this->events->trigger('registeredCustomerPersisted', $this, ['customer' => $customer]);
            //return $customer;
        } catch (\Exception $e) {

            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function removeSessionOptionalData()
    {
        $this->optionalForm->clearRegisteredData();
    }

    public function isRegistrationCompleted($customer){
        $completed = false;
        //return ($customer instanceOf Customers && $customer->getTaxCode() != null);
        if ($customer instanceOf Customers){
            return is_null($this->deactivationService->getAllActive($customer, CustomerDeactivation::REGISTRATION_NOT_COMPLETED));
        }
        return $completed;
    }

    public function getSignupCustomerSession()
    {
        $signupSession = new Container('newSignup');
        return $signupSession->offsetGet("customer");
    }

/**
     * Assin a promocode (standard, onece, memberGetMember) to customer
     * 
     * @param string $promoCodeName
     * @param Customers $customer
     * @return type
     * @throws \Exception
     */
    private function assingPromocode($promoCodeName, Customers $customer){
        $result = null;
        $promoCode = null;
        $promoCodeOnce = null;

        if($promoCodeName != '') {
            if($this->promoCodesService->isValid($promoCodeName)) { // it's a standard promocode
                $promoCode = $this->promoCodesService->getPromoCode($promoCodeName);
                $customerBonus = CustomersBonus::createFromPromoCode($promoCode);
                $customerBonus->setCustomer($customer);
                $this->entityManager->persist($customerBonus);
                $result = $customerBonus;

                // promo codes has a discount percentage
                if ($promoCode->discountPercentage() > 0) {
                    $discountPercentage = max(
                        $customer->getDiscountRate(),
                        $promoCode->discountPercentage()
                    );
                    $customer->setDiscountRate($discountPercentage);
                }
            } elseif ($this->promoCodesOnceService->isValid($promoCodeName)) { // it's an once promocode
                $promoCodeOnce = $this->promoCodesOnceService->usePromoCode($customer, $promoCodeName);

                if(!is_null($promoCodeOnce)){
                    $promoCodeInfo = $promoCodeOnce->getPromoCodesInfo();
                    $customer->setDiscountRate($promoCodeInfo->discountPercentage());
                    $result = $promoCodeOnce;
                } else { // error in set promocode once
                    throw new \Exception('Promocode once '.$promoCodeName.' not found,');
                }
            } elseif ($this->promoCodesMemberGetMemberService->isValid($promoCodeName)) {  // it's a MemberGetMember promocode
                $result = $this->assignPromocodeMemberGetMember($promoCodeName, $customer);
            }
        }

        if(is_null($result)){   // if no bonus assigned
            $total = $this->subscriptionBonus['total'];
            $subBonus = CustomersBonus::createBonus(
                $customer,
                $total, //$this->subscriptionBonus['total'],
                $this->subscriptionBonus['description'],
                $this->subscriptionBonus['valid-to']
            );
            $this->entityManager->persist($subBonus);
        }

        $this->entityManager->persist($customer);
        return $result;
    }

    /**
     * Assign a promocode oncet for Member get Member
     * @param string $promoCodeName
     * @param Customers $customer
     */
    private function assignPromocodeMemberGetMember($promoCodeName, Customers $customer) {
        $result = null;
        $newPromoCodeOnce = $this->promoCodesMemberGetMemberService->createPromoCodeOnceForNewCustomer($promoCodeName, $customer);

        if(!is_null($newPromoCodeOnce)) {   // assign a bonus to old customer
            $promoCodeOnce = $this->promoCodesOnceService->usePromoCode($customer, $newPromoCodeOnce->getPromocode());
            $promoCodeInfo = $promoCodeOnce->getPromoCodesInfo();
            $customer->setDiscountRate($promoCodeInfo->discountPercentage());
            $customerOldBonus = $this->promoCodesMemberGetMemberService->assignBonusForOldCustomer($customer);

            if(!is_null($customerOldBonus)){    // send an email to old customer
                $customerOld = $customerOldBonus->getCustomer();
                $mail = $this->emailService->getMail(22, 'it');
                $content = sprintf(
                    $mail->getContent(),
                    $customerOld->getName().' '. $customerOld->getSurname(),
                    $customerOldBonus->getTotal()
                );

                $this->emailService->sendEmail(
                    $customerOld->getEmail(),
                    $mail->getSubject(),
                    $content
                );
            }

            $result = $promoCodeOnce;
        }
    }

}
