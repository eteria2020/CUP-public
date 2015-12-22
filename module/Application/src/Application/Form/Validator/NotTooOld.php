<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class NotTooOld extends AbstractValidator
{
    /**
     * @var string
     */
    private $minYear;

    /**
     * @var string
     */
    const TOO_OLD = 'notTooOld';

    /**
     * @var array
     */
    protected $messageTemplates;

    /**
     * @inheritdoc
     */
    public function __construct($options = null)
    {
        $this->minYear = date_create('100 years ago')->format('Y');
        $this->messageTemplates = [
            self::TOO_OLD => "Date inferiori al " . $this->minYear . " non sono accettate"
        ];
        parent::__construct($options);
    }

    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $date = date_create($value);
        $oldest = date_create("first day of January " . $this->minYear);

        if ($date < $oldest) {
            $this->error(self::TOO_OLD);
            return false;
        }

        return true;
    }
}
