<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\AuthorityService;
use SharengoCore\Service\CountriesService;
use SharengoCore\Service\CustomersService;
use Zend\Form\Fieldset;
use Zend\Mvc\I18n\Translator;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class DriverFieldset extends Fieldset implements InputFilterProviderInterface
{
    private $authorityService;
    private $avoid;

    public function __construct(
        Translator $translator,
        HydratorInterface $hydrator,
        CountriesService $mondoService,
        CustomersService $customersService,
        AuthorityService $authorityService,
        $avoidValidatorDriverLicense = null
    ) {
        $this->customersService = $customersService;
        $this->authorityService = $authorityService;
        $this->avoid = $avoidValidatorDriverLicense;

        parent::__construct('driver', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new Customers());

        $this->add([
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id'
            ]
        ]);

        $this->add([
            'name' => 'driverLicense',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'driverLicense',
                'class' => 'required',
                'placeholder' => $translator->translate('XX123412341223'),
            ],
            'options' => [
                'label' => $translator->translate('Patente')
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseAuthority',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'driverLicenseAuthority',
                'placeholder' => 'UCO',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Rilasciato da (autorità)'),
                'value_options' => $this->authorityService->getAllAuthorities()
            ]

        ]);

        $this->add([
            'name' => 'driverLicenseReleaseDate',
            'type' => 'Zend\Form\Element\Date',
            'attributes' => [
                'id' => 'driverLicenseReleaseDate',
                'class' => 'required datepicker-date',
                'max' => date_create()->format('d-m-Y'),
                'placeholder' => $translator->translate('dd-mm-aaaa'),
                'type' => 'text'
            ],
            'options' => [
                'label' => $translator->translate('Rilasciato il'),
                'format' => 'd-m-Y'
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseName',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'driverLicenseName',
                'placeholder' => $translator->translate('Es. Mario'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Nome sulla patente')
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseSurname',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'driverLicenseSurname',
                'placeholder' => $translator->translate('Es. Rossi'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Cognome sulla patente')
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseCountry',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'driverLicenseCountry',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Rilasciata da (nazione)'),
                'value_options' => $mondoService->getAllCountries()
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseExpire',
            'type' => 'Zend\Form\Element\Date',
            'attributes' => [
                'id' => 'driverLicenseExpire',
                'class' => 'required datepicker-date',
                'min' => date_create()->format('d-m-Y'),
                'placeholder' => $translator->translate('dd-mm-aaaa'),
                'type' => 'text'
            ],
            'options' => [
                'label' => $translator->translate('Data di scadenza'),
                'format' => 'd-m-Y'
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseCategories',
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'attributes' => [
                'id' => 'driverLicenseCategories'
            ],
            'options' => [
                'label' => $translator->translate('Categoria/e patente'),
                'value_options' => [
                    'A' => 'A',
                    'B' => 'B'
                ]
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseForeign',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => [
                'id' => 'driverLicenseForeign'
            ],
            'options' => [
                'use_hidden_element' => true,
                'checked_value' => 'true',
                'unchecked_value' => 'false'
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        if (is_null($this->avoid)) {
            $optionValidatorDriverLicense = [
                'customerService' => $this->customersService
            ];
        } else {
            $optionValidatorDriverLicense = [
                'customerService' => $this->customersService,
                'avoid' => [
                    $this->avoid
                ]
            ];
        }

        return [
            'driverLicense' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'min' => 6,
                            'max' => 32
                        ]
                    ],
                    [
                        'name' => 'Application\Form\Validator\DuplicateDriversLicense',
                        'options' => $optionValidatorDriverLicense
                    ]
                ]
            ],
            'driverLicenseAuthority' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 3
                        ]
                    ]
                ]
            ],
            'driverLicenseReleaseDate' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\DateFormat'
                    ],
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd-m-Y'
                        ]
                    ],
                    [
                        'name' => 'Application\Form\Validator\NotFutureDate'
                    ]
                ]
            ],
            'driverLicenseName' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 32
                        ]
                    ]
                ]
            ],
            'driverLicenseSurname' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 32
                        ]
                    ]
                ]
            ],
            'driverLicenseCountry' => [
                'required' => true
            ],
            'driverLicenseForeign' => [
                'required' => true
            ],
            'driverLicenseExpire' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\DateFormat'
                    ],
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd-m-Y'
                        ]
                    ],
                    [
                        'name' => 'Application\Form\Validator\DateFromToday'
                    ]
                ]
            ],
            'driverLicenseCategories' => [
                'required' => true,
            ]
        ];
    }
}
