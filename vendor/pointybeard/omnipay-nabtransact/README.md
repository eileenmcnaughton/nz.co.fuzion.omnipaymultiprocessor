# Omnipay: NAB Transact

**NAB Transact API driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements NAB Transact support for Omnipay.

**IMPORTANT: This is a very early alpha release, so it's pretty rough, and is likely to be buggy. Please do not use this in production environments. If anyone wants to help me out, that would be awesome.**


## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "pointybeard/omnipay-nabtransact": "~0.1"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

```php
    include(__DIR__ . '/../vendor/autoload.php');
    use Omnipay\Omnipay;
    use Omnipay\Common as OmnipayCommon;

    $g = Omnipay::create('NABTransact_Periodic');
    $g->initialize([
        'merchantID' => 'XYZ0010',
        'password' => 'abcd1234',
        'testMode' => true,
    ]);

    // Add a customer
    $request = $g->createCard(['card' => [
            'number' => '4111111111111111',
            'expiryMonth' => '02',
            'expiryYear' => '18',
            'cvv' => '123',
        ]
    ]);
    $response = $request->send();

    // Update a customer
    $request = $g->updateCard([
        'customerReference' => $response->getCustomerReference(),
        'card' => [
            'number' => '4444333322221111',
            'expiryMonth' => '03',
            'expiryYear' => '16'
        ]
    ]);
    $response = $request->send();

    // Trigger a payment
    $request = $g->purchase([
        'customerReference' => $response->getCustomerReference(),
        'transactionReference' => 'Test Trigger of CC Payment',
        'transactionAmount' => '1234',
        'transactionCurrency' => 'AUD',
    ]);

    $response = $request->send();

    // Delete a customer
    $request = $g->deleteCard(['customerReference' => $response->getCustomerReference()]);
    $response = $request->send();

```

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Out Of Scope

This currently supports the 'addcrn', 'editcrn', 'deletecrn' and 'trigger' actions of NAB's "Customer Management and Payment scheduling" XML API. Eventually it will support scheduling and triggering a DD payment.

It does not support the "XML API for Payments" API, however will eventually.

## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/pointybeard/omnipay-nabtransact/issues),
or better yet, fork the library and submit a pull request.