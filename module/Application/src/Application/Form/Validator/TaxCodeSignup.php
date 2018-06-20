<?php

namespace Application\Form\Validator;

use CodiceFiscale\Checker;
use Zend\Validator\AbstractValidator;

class TaxCodeSignup extends AbstractValidator
{
    const NOTVALID = 'notvalid';
    const EIGHTEEN = 'eighteen';

    protected $messageTemplates = array(
        self::NOTVALID => "Codice fiscale non valido",
        self::EIGHTEEN  => "Età non consentita per la guida",
    );

    public function isValid($value)
    {
        $this->setValue($value);

        $isValid = true;

        $chk = new Checker();

        if ($chk->isFormallyCorrect($value)) {
            $eighteen = date_create("-18 years");
            $birthYear = $chk->getYearBirth();

            if ($birthYear > (date('y')-18)){
                $birthYear = '19'.$birthYear;
            } else {
                $birthYear = '20'.$birthYear;
            }
            $birthDate = $birthYear.'-'.$chk->getMonthBirth().'-'.$chk->getDayBirth();
            $date = date_create($birthDate);

            if ($date > $eighteen) {
                $this->error(self::EIGHTEEN);
                $isValid = false;
            }
            $minYear = date_create('100 years ago')->format('Y');
            $oldest = date_create("first day of January " . $minYear);

            if ($date < $oldest) {
                $this->error(self::EIGHTEEN);
                $isValid = false;
            }
        } else{
            $this->error(self::NOTVALID);
            $isValid = false;
        }

        return $isValid;
    }
}
