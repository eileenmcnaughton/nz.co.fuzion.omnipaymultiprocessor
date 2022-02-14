<?php
namespace Omnipay\FirstAtlanticCommerce\Exception;

class MethodNotSupported extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $this->message = "Method ($message) not supported";
    }
}
