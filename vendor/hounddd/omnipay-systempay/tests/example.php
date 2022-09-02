<?php

$gw = Omnipay::create('SystemPay');
$gw->setCertificate('1234567890');
$gw->setTestMode(true);

$card = new CreditCard(array(
    'firstName' => 'John',
    'lastName' => 'Doe',
    'billingAddress1' => '1 rue de la gare',
    'billingCity' => 'MACON',
    'billingPostcode' => '71000',
    'billingCountry' => 'FRANCE',
    'billingPhone' => '0600000000',
    'email' => 'john.doe@gmail.com'
));

$response = $gw->purchase(array(
    'amount'   => '10.00',
    'currency' => 'EUR',
    'card'     => $card
))->send();

// Process response
if ($response->isSuccessful()) {

    // Payment was successful
    print_r($response);

} elseif ($response->isRedirect()) {

    // Redirect to offsite payment gateway
    $response->redirect();

} else {

    // Payment failed
    echo $response->getMessage();
}
