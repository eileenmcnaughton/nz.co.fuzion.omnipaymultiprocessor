<?php

namespace Omnipay\NMI\Message;

/**
* NMI Direct Post Update Card Request
*/
class DirectPostUpdateCardRequest extends DirectPostCreateCardRequest
{
    protected $customer_vault = 'update_customer';
}
