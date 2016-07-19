<?php

namespace Application\Controller;

use Application\Form\PromoCodeForm;
use SharengoCore\Entity\Customers;
use SharengoCore\Entity\CustomersBonus;
use SharengoCore\Entity\PromoCodes;
use SharengoCore\Exception\BonusAssignmentException;
use SharengoCore\Exception\CodeAlreadyUsedException;
use SharengoCore\Exception\NotAValidCodeException;
use SharengoCore\Service\CarrefourService;
use SharengoCore\Service\CustomersBonusPackagesService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\PromoCodesService;

use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class AdditionalServicesController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customerService;

    /**
     * @var CarrefourService
     */
    private $carrefourService;

    /**
     * @var PromoCodeForm
     */
    private $promoCodeForm;

    /**
     * @var PromoCodesService
     */
    private $promoCodeService;

    /**
     * @var CustomersBonusPackagesService
     */
    private $customersBonusPackagesService;

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * @param CustomersService $customerService
     * @param CarrefourService $carrefourService
     * @param Form $promoCodeForm
     * @param PromoCodesService $promoCodeService
     * @param CustomersBonusPackagesService $customersBonusPackagesService
     * @param AuthenticationService $authService
     */
    public function __construct(
        CustomersService $customersService,
        CarrefourService $carrefourService,
        Form $promoCodeForm,
        PromoCodesService $promoCodeService,
        CustomersBonusPackagesService $customersBonusPackagesService,
        AuthenticationService $authService
    ) {
        $this->customersService = $customersService;
        $this->carrefourService = $carrefourService;
        $this->promoCodeForm = $promoCodeForm;
        $this->promoCodeService =  $promoCodeService;
        $this->customersBonusPackagesService = $customersBonusPackagesService;
        $this->authService = $authService;
    }

    public function additionalServicesAction()
    {
        $form = $this->promoCodeForm;

        if ($this->getRequest()->isPost()) {
            $customer = $this->authService->getIdentity();
            $postData = $this->getRequest()->getPost()->toArray();
            $form->setData($postData);

            // Check if form represents a valid Promocode
            if ($form->isValid()) {
                try {
                    $promoCode = $this->promoCodeService->getPromoCode($postData['promocode']['promocode']);
                    $this->customersService->addBonusFromPromoCode($customer, $promoCode);
                    $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');

                } catch (BonusAssignmentException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo');
                }

            // Check if form represents a valid Carrefour code
            } else {
                try {
                    $this->carrefourService->addFromCode($customer, $postData['promocode']['promocode']);
                    $this->flashMessenger()->addSuccessMessage('Operazione completata con successo!');

                } catch (NotAValidCodeException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());

                } catch (CodeAlreadyUsedException $e) {
                    $this->flashMessenger()->addErrorMessage('Il codice inserito è già stato utilizzato');

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo');
                }
            }

            return $this->redirect()->toRoute('area-utente/additional-services');
        }

        $bonusPackages = $this->customersBonusPackagesService->getAvailableBonusPackges();

        return new ViewModel([
            'promoCodeForm' => $form,
            'bonusPackages' => $bonusPackages
        ]);
    }
}
