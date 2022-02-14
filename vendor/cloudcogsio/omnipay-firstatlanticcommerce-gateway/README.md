# Omnipay - First Atlantic Commerce

**First Atlantic Commerce gateway for the Omnipay PHP payment processing library**

![Packagist License](https://img.shields.io/packagist/l/cloudcogsio/omnipay-firstatlanticcommerce-gateway) ![Packagist Version](https://img.shields.io/packagist/v/cloudcogsio/omnipay-firstatlanticcommerce-gateway) ![Packagist PHP Version Support (specify version)](https://img.shields.io/packagist/php-v/cloudcogsio/omnipay-firstatlanticcommerce-gateway/dev-master) ![GitHub issues](https://img.shields.io/github/issues/cloudcogsio/omnipay-firstatlanticcommerce-gateway) ![GitHub last commit](https://img.shields.io/github/last-commit/cloudcogsio/omnipay-firstatlanticcommerce-gateway) 

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements First Atlantic Commerce (FAC) support for Omnipay.

## Installation
Via Composer

``` bash
$ composer require cloudcogsio/omnipay-firstatlanticcommerce-gateway
```
## Gateway Operation Defaults
This gateway driver operates in 3DS mode by default and requires a callback URL to be provided via the '**setReturnUrl**' method. The return URL must then implement the '**acceptNotification**' method to capture the transaction response from FAC.

The gateway can process non-3DS transactions by explicitly turning off 3DS during gateway configuration. 
```php
$gateway->set3DS(false);
```

## Usage
For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

### Non-3DS Transactions (Direct Integration)
``` php

use Omnipay\Omnipay;
try {
    $gateway = Omnipay::create('FirstAtlanticCommerce_FAC');
    $gateway
        ->setTestMode(true)
        ->setIntegrationOption(\Omnipay\FirstAtlanticCommerce\Constants::GATEWAY_INTEGRATION_DIRECT)
        ->setFacId('xxxxxxxx')
        ->setFacPwd('xxxxxxxx')
        ->set3DS(false);

    $cardData = [
        'number' => '4111111111111111',
        'expiryMonth' => '01',
        'expiryYear' => '2025',
        'cvv' => '123'
    ];

    $transactionData = [
        'card' => $cardData,
        'currency' => 'USD',
        'amount' => '1.00',
        'transactionId' => 'OrderNo-2100001'
    ];

    $response = $gateway->purchase($transactionData)->send();

    if($response->isSuccessful())
    {
        // Verify response
        $response->verifySignature();
        
        // Purchase was succussful, continue order processing
        ...
    }
} catch (Exception $e){
    $e->getMessage();
}
```

### 3DS Transactions (Direct Integration)
'**returnUrl**' required. URL must be **https://**
``` php

use Omnipay\Omnipay;
try {
    $gateway = Omnipay::create('FirstAtlanticCommerce_FAC');
    $gateway
        ->setTestMode(true)
        ->setIntegrationOption(\Omnipay\FirstAtlanticCommerce\Constants::GATEWAY_INTEGRATION_DIRECT)
        ->setFacId('xxxxxxxx')
        ->setFacPwd('xxxxxxxx')
        ->set3DS(true)
        
        // **Required and must be https://
        ->setReturnUrl('https://localhost/accept-notification.php');

    $cardData = [
        'number' => '4111111111111111',
        'expiryMonth' => '01',
        'expiryYear' => '2025',
        'cvv' => '123'
    ];

    $transactionData = [
        'card' => $cardData,
        'currency' => 'USD',
        'amount' => '1.00',
        'transactionId' => 'OrderNo-2100001'
    ];

    $response = $gateway->purchase($transactionData)->send();

    if($response->isRedirect())
    {
	    // Redirect to continue 3DS verification
        $response->redirect();
    }
    else 
    {
	    // 3DS transaction failed setup, show error reason.
        echo $response->getMessage();
    }
} catch (Exception $e){
    $e->getMessage();
}
```
***accept-notification.php***
Accept transaction response from FAC.
```php
$gateway  = Omnipay::create('FirstAtlanticCommerce_FAC');
$gateway    
    // Password is required to perform response signature verification
    ->setFacPwd('xxxxxxxx');
    
// Signature verification is performed implicitly once the gateway was initialized with the password.
$response = $gateway->acceptNotification($_POST)->send();

if($response->isSuccessful())
{       
    // Purchase was succussful, continue order processing
    ...
}
else 
{
    // Transaction failed
    echo $response->getMessage();
}
```

## Support

If you are having general issues with Omnipay, we suggest posting on [Stack Overflow](http://stackoverflow.com/). Be sure to add the [omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.


If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/cloudcogsio/omnipay-firstatlanticcommerce-gateway/issues), or better yet, fork the library and submit a pull request.
