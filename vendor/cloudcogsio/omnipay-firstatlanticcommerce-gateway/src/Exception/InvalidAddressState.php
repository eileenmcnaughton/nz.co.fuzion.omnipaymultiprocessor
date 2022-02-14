<?php
namespace Omnipay\FirstAtlanticCommerce\Exception;

class InvalidAddressState extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $this->message = "Invalid state ($message)";
    }
}
