<?php

namespace Application\Service;

use Application\Form\RegistrationForm;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\ProviderAuthenticatedCustomer;
use SharengoCore\Service\EmailService;

use Hybrid_Auth;
use ScnSocialAuth\Options\ModuleOptions;
use Doctrine\ORM\EntityManager;
use Zend\Session\Container;
use Zend\View\HelperPluginManager;

class ProviderAuthenticationService
{
    const SESSION_KEY = 'providerAuthenticatedCustomer';

    const UUID = 'uuid';

    /**
     * @var ModuleOptions
     */
    private $options;

    /**
     * @var Hybrid_Auth
     */
    private $hybridAuth;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var HelperPluginManager
     */
    private $viewHelperManager;

    public function __construct(
        ModuleOptions $options,
        Hybrid_Auth $hybridAuth,
        EntityManager $entityManager,
        EmailService $emailService,
        HelperPluginManager $viewHelperManager
    ) {
        $this->options = $options;
        $this->hybridAuth = $hybridAuth;
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
        $this->viewHelperManager = $viewHelperManager;
    }

    /**
     * @param string $provider
     * @return ProviderAuthenticatedCustomer
     * @throws \Exception
     */
    public function authenticateWithProvider($provider)
    {
        if (!in_array($provider, $this->options->getEnabledProviders())) {
            throw new \Exception();
        }

        $adapter = $this->hybridAuth->authenticate($provider);
        $userProfile = $adapter->getUserProfile();

        return ProviderAuthenticatedCustomer::fromUserProfile($provider, $userProfile);
    }

    /**
     * @param ProviderAuthenticatedCustomer $customer
     */
    public function welcomeCustomer(ProviderAuthenticatedCustomer $customer)
    {
        // first thing we save the customer
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        // then we send an email to the customer
        if ($customer->hasEmail()) {
            /** @var callable $url */
            $url = $this->viewHelperManager->get('url');
            /** @var callable $serverUrl */
            $serverUrl = $this->viewHelperManager->get('serverUrl');

            $content = sprintf(
                file_get_contents(__DIR__.'/../../../view/emails/provider-authenticated-welcome.html'),
                $serverUrl(),
                $serverUrl().$url('scn-social-auth-user/register', ['id' => $customer->id()])
            );

            $attachment = [
                'banner.jpg' => __DIR__.'/../../../../../public/images/banner_benvenuto.jpg'
            ];

            $this->emailService->sendEmail(
                $customer->email(),
                'Benvenuto in Share’ngo, ecco come completare la tua iscrizione',
                $content,
                $attachment
            );
        }
    }

    public function warmupCache(ProviderAuthenticatedCustomer $customer)
    {
        $customerData = new Customers();

        $customerData->setEmail($customer->email());
        $customerData->setName($customer->firstName());
        $customerData->setSurname($customer->lastName());
        $customerData->setBirthdate(date_create($customer->birthYear().'/'.$customer->birthMonth().'/'.$customer->birthDay()));
        $customerData->setPhone($customer->phone());

        $registrationSessionContainer = new Container(RegistrationForm::SESSION_KEY);
        $registrationSessionContainer->offsetSet(RegistrationForm::FORM_DATA, $customerData);

        $providerSessionContainer = new Container(static::SESSION_KEY);
        $providerSessionContainer->offsetSet(self::UUID, $customer->id());
    }
}
