<?php
namespace Omnipay\FirstAtlanticCommerce\Exception;

class CurrencyNotConfigured extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $this->message = "Currency ($message) not configured";
    }
}