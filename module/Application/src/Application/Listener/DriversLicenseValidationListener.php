<?php

namespace Application\Listener;

use SharengoCore\Service\CountriesService;
use MvLabsDriversLicenseValidation\Service\EnqueueValidationService;
use SharengoCore\Service\CustomersService;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\EventInterface;

final class DriversLicenseValidationListener implements SharedListenerAggregateInterface
{
    /**
     * @var EnqueueValidationService $enqueueValidationService
     */
    private $enqueueValidationService;

    /**
     * @var CustomersService $customersService
     */
    private $customersService;

    /**
     * @var CountriesService $countriesService
     */
    private $countriesService;

    public function __construct(
        EnqueueValidationService $enqueueValidationService,
        CustomersService $customersService,
        CountriesService $countriesService
    ) {
        $this->enqueueValidationService = $enqueueValidationService;
        $this->customersService = $customersService;
        $this->countriesService = $countriesService;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'Application\Controller\UserController',
            'registrationCompleted',
            [$this, 'validateDriversLicense']
        );

        $this->listeners[] = $events->attach(
            'Application\Controller\UserAreaController',
            'driversLicenseEdited',
            [$this, 'validateDriversLicense']
        );

        $this->listeners[] = $events->attach(
            'Application\Controller\UserAreaController',
            'taxCodeEdited',
            [$this, 'validateDriversLicense']
        );
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function validateDriversLicense(EventInterface $e)
    {
        $data = $e->getParams();

        $customer = $this->customersService->findByEmail($data['email'])[0];
        
        // we do not request the validation of the drivers license to the
        // motorizzazione civile is the customer has a foreign drivers license
        if (!$this->customersService->customerNeedsToAcceptDriversLicenseForm($customer)) {
            $data['birthCountryMCTC'] = $this->countriesService->getMctcCode($data['birthCountry']);
            
            $data['birthProvince'] = $this->changeProvinceForValidationDriverLicense($data);            
            
            $this->enqueueValidationService->validateDriversLicense($data);
        }
    }
    
    /*This method returns a different birthPrertince,
     * so if birthProvince == 'MB' changes to 'MI'
     * because after creating an MB province the whole city was under the MI province.
     * While birthProvince == 'LC' and the city is in array$municipalities_lecco_special
     * sets birthProvince = 'BG' because the city in $municipalities_lecco_special
     * was under the province of BG, all the More cities were under the province of CO
     */
    private function changeProvinceForValidationDriverLicense($data) {
        switch ($data['birthProvince']){
            //Monza-Brinaza --> Milano
            case 'MB':
                $birthProvince = 'MI';
                break;
            //Lecco --> Bergamo || Como
            case 'LC':
                $municipalities_lecco_special = array("CALOLZIOCORTE", "CARENNO", "ERVE", "MONTE MARENZO", "VERCURAGO");
                if (in_array($data['birthTown'], $municipalities_lecco_special))
                    $birthProvince = 'BG';
                else
                    $birthProvince = 'CO';
                break;
            //Biella --> Vercelli
            case 'BI':
                $birthProvince = 'VC';
                break;
            //Barletta-Andria-Trani --> Bari
            case 'BT':
                $birthProvince = 'BA';
                break;
            //Forlì-Cesena --> Forlì(old)
            case 'FC':
                $birthProvince = 'FO';
                break;
            //Pesaro-Urbino --> Pesaro(old)
            case 'PU':
                $birthProvince = 'PS';
                break;
            //Vibo-Valentia --> Catanzaro
            case 'VV':
                $birthProvince = 'CZ';
                break;
            //Carbonia-Iglesias --> Cagliari
            case 'CI':
                $birthProvince = 'CA';
                break;
        }
        return $birthProvince;
    }

}
