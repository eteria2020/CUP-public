<?php

namespace Application\Form\Validator;

use CodiceFiscale\Calculator;
use Zend\Validator\AbstractValidator;

class CoherentTaxCode extends AbstractValidator
{
    const INCOHERENT_TAX_CODE = 'InchoherentTaxCode';

    protected $messageTemplates = [
        self::INCOHERENT_TAX_CODE => "Il codice fiscale non è coerente con gli altri dati indicati"
    ];

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * @var CustomerService
     */
    private $customersService;

    public function __construct($options)
    {
        parent::__construct();
        $this->translator = new \Zend\I18n\Translator\Translator();
        $this->customersService = $options['customerService'];
        $messageTemplates[ self::INCOHERENT_TAX_CODE] = $this->translator->translate("Il codice fiscale non è coerente con gli altri dati indicati");
    }

    public function isValid($value, $context = null)
    {

        $this->setValue($value);

        $genderAdapter = [
            'male' => 'M',
            'female' => 'F'
        ];
        
        if (!preg_match("/(\d{2})-(\d{2})-(\d{4})/", $context['birthDate'])) {
            $this->error(self::INCOHERENT_TAX_CODE);
            return false;
        }
        
        $calculator = new Calculator();

        if (!is_null($context['birthCountry']) && !is_null($context['birthProvince']) && !is_null($context['birthTown'])){
            $cadastralCode = $this->customersService->findCadastralCodeByCountryProviceTown($context['birthCountry'], $context['birthProvince'], $context['birthTown']);
            if(!is_null($cadastralCode)) {
                $computedTaxCode = $calculator->calcola(
                    $context['name'],
                    $context['surname'],
                    $genderAdapter[$context['gender']],
                    new \DateTime($context['birthDate']),
                    $cadastralCode);

                    if($computedTaxCode!=$value) {
                        $this->error(self::INCOHERENT_TAX_CODE);
                        return false;
                    }

            } else {
                $this->error(self::INCOHERENT_TAX_CODE);
                return false;
            }

        } else { // this check only name,surname,gender and birth date
            $computedTaxCode = $calculator->calcola(
                $context['name'],
                $context['surname'],
                $genderAdapter[$context['gender']],
                new \DateTime($context['birthDate']),
                'A123' //fake code just to satisfy the interface
            );

            if (substr(strtoupper($value), 0, 11) !== substr(strtoupper($computedTaxCode), 0, 11)) {
                $this->error(self::INCOHERENT_TAX_CODE);
                return false;
            }
        }

        return true;
    }
}
