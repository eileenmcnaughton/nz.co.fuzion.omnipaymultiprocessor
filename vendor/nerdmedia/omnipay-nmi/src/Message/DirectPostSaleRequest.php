<?php
namespace Omnipay\NMI\Message;

/**
* NMI Direct Post Sale Request
*/
class DirectPostSaleRequest extends DirectPostAuthRequest
{
    protected $type = 'sale';
}
