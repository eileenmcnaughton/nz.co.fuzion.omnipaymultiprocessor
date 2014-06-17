<?php

namespace Omnipay\Buckaroo\Message;

/**
 * Buckaroo PayPal Purchase Request
 */
class PayPalPurchaseRequest extends PurchaseRequest
{
    protected $endpoint = 'https://payment.buckaroo.nl/gateway/paypal_payment.asp';
}
