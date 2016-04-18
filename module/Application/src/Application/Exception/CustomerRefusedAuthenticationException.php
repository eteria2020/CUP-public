<?php

namespace Application\Exception;

class CustomerRefusedAuthenticationException extends \Exception
{
    protected $message = 'Customer refused authentication';
}
