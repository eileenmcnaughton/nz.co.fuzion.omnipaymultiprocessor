<?php

namespace Omnipay\SecurePay\Message;

/**
 * SecurePay Direct Post Abstract Request
 */
abstract class DirectPostAbstractRequest extends AbstractRequest
{
    public $testEndpoint = 'https://test.api.securepay.com.au/directpost/authorise';
    public $liveEndpoint = 'https://api.securepay.com.au/directpost/authorise';
}
