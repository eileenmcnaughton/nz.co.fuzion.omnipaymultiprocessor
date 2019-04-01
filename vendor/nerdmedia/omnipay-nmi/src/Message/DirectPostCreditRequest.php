<?php
namespace Omnipay\NMI\Message;

/**
* NMI Direct Post Credit Request
*/
class DirectPostCreditRequest extends DirectPostAuthRequest
{
    protected $type = 'credit';
}
