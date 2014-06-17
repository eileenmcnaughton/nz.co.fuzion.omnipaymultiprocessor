<?php

namespace Omnipay\Buckaroo\Message;

/**
 * Buckaroo iDeal Purchase Request
 */
class IdealPurchaseRequest extends PurchaseRequest
{
    protected $endpoint = 'https://payment.buckaroo.nl/gateway/ideal_payment.asp';
}
