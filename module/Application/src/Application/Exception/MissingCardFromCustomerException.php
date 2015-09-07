<?php

namespace Application\Exception;

class MissingCardFromCustomerException extends \DomainException
{
    protected $message = 'MissingCardFromCustomerException: found customer without card';
}
