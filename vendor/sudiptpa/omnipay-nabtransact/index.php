<?php

require __DIR__.'/vendor/autoload.php';

use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;

$gateway = Omnipay::create('NABTransact_SecureXML');

$gateway->setMerchantId('XYZ0010');
$gateway->setTransactionPassword('abcd1234');

$gateway->setTestMode(true);

$card = new CreditCard([
    'firstName'   => 'Sujip',
    'lastName'    => 'Thapa',
    'number'      => '4444333322221111',
    'expiryMonth' => '12',
    'expiryYear'  => date('Y'),
    'cvv'         => '123',
]);

$response = $gateway->purchase([
    'amount'        => '12.00',
    'transactionId' => 'ORDER-ZYX8789',
    'currency'      => 'AUD',
    'card'          => $card,
])->send();

$message = sprintf(
    'Transaction with reference code  (%s) - %s',
    $response->getTransactionReference(),
    $response->getMessage()
);

echo $message;
