<?php
require '../vendor/autoload.php';

use Omnipay\Common\CreditCard;
use Omnipay\Gopay\Api\GopayHelper;
use Omnipay\Omnipay;

if ($argc != 3) {
    echo "Usage: ${argv[0]} GO_ID SECURE_KEY";
    exit(1);
}

$goId = $argv[1];
$secureKey = $argv[2];

function createGateway($goId, $secureKey)
{
    $gateway = Omnipay::create('Gopay');
    $gateway->setTestMode(true);
    $gateway->setGoId($goId);
    $gateway->setSecureKey($secureKey);
    return $gateway;
}

$gateway = createGateway($goId, $secureKey);

$purchaseData = array(
    'amount' => '10.00',
    'currency' => 'CZK',
    'description' => 'Product Description',
    'returnUrl' => 'https://www.example.com/return',
    'cancelUrl' => 'https://www.example.com/cancel',
    'transactionId' => '98765',
    'card' => new CreditCard(array(
        'email' => 'test@email.com'
    )));

echo "Sending purchase request:";
print_r($purchaseData);

$purchaseResponse = null;

try {
    $purchaseResponse = $gateway->purchase($purchaseData)->send();
    echo $gateway->getSoapClient()->__getLastRequest();
} catch (Exception $e) {
    echo $gateway->getSoapClient()->__getLastRequest();
    throw $e;
}

echo "Received response:";
print_r($purchaseResponse->getData());

if (!$purchaseResponse->isSuccessful() && !$purchaseResponse->isRedirect()) {
    print "Response is not successful and not a redirect: " . $purchaseResponse->getMessage();
    exit(1);
}

// Simulate notification
$_GET['paymentSessionId'] = $purchaseResponse->getData()->paymentSessionId;
$_GET['targetGoId'] = $purchaseResponse->getData()->targetGoId;
$_GET['orderNumber'] = $purchaseResponse->getData()->orderNumber;
$_GET['encryptedSignature'] = GopayHelper::getPaymentIdentitySignature($_GET['targetGoId'],
    $_GET['paymentSessionId'],
    null,
    $_GET['orderNumber'],
    $secureKey);

// Recreate gateway to pick up the GET parameters
$gateway = createGateway($goId, $secureKey);

try {
    $completePurchaseResponse = $gateway->completePurchase()->send();
    echo $gateway->getSoapClient()->__getLastRequest();
} catch (Exception $e) {
    echo $gateway->getSoapClient()->__getLastRequest();
    throw $e;
}

echo "Received response to completePurchase (payment status):";
print_r($completePurchaseResponse->getData());
