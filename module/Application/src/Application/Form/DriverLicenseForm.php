<?php

namespace Application\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;

class DriverLicenseForm extends Form
{
    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    private $entityManager;

    public function __construct(
        Translator $translator,
        DriverFieldset $driverFieldset,
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        parent::__construct('driver');
        $this->setAttribute('method', 'post');

        $this->add($driverFieldset);

        $this->add([
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
}
