# Omnipay: First Atlantic Commerce

**First Atlantic Commerce driver for the Omnipay PHP payment processing library**

[![Source Code](http://img.shields.io/badge/source-emperorjm/omnipay--first--atlantic--commerce-blue.svg?style=flat-square)](https://github.com/emperorjm/omnipay-first-atlantic-commerce) [![Latest Version](https://img.shields.io/github/release/emperorjm/omnipay-first-atlantic-commerce.svg?style=flat-square)](https://github.com/emperorjm/omnipay-first-atlantic-commerce/releases) [![Software License](https://img.shields.io/github/license/emperorjm/omnipay-first-atlantic-commerce.svg?style=flat-square)](https://github.com/emperorjm/omnipay-first-atlantic-commerce/blob/master/LICENSE)

[![Total Downloads](https://img.shields.io/packagist/dt/emperorjm/omnipay-first-atlantic-commerce.svg?style=flat-square)](https://packagist.org/packages/emperorjm/omnipay-first-atlantic-commerce/)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements First Atlantic Commerce support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file and update or install directly with composer require:

```
$ composer require emperorjm/omnipay-first-atlantic-commerce
```
This package strives to use Semantic Versioning as explained [here](http://semver.org/).

## Basic Usage

The following gateways are provided by this package:

* FirstAtlanticCommerce

This package implements the following methods:

* ``authorize($options)`` – authorize an amount on the customer’s card.
* ``capture($options)`` – capture an amount you have previously authorized.
* ``purchase($options)`` – authorize and immediately capture an amount on the customer’s card.
* ``refund($options)`` – refund an already processed (settled) transaction.
* ``void($options)`` – reverse a previously authorized (unsettled) transaction.
* ``status($options)`` – check the status of a previous transaction.
* ``createCard($options)`` – create a stored card and return the reference token for future transactions.
* ``updateCard($options)`` – update a stored card's expiry or customer reference.

For general usage instructions, please see the [Omnipay documentation](http://omnipay.thephpleague.com/).
For information on the parameters needed for each request, see the class documentation for that request in the Message folder.

### Basic Example

```php
use Omnipay\Omnipay;

// Setup payment gateway
$gateway = Omnipay::create('FirstAtlanticCommerce');
$gateway->setMerchantId('123456789');
$gateway->setMerchantPassword('abc123');

// Example form data
$formData = [
    'number'      => '4242424242424242',
    'expiryMonth' => '6',
    'expiryYear'  => '2016',
    'cvv'         => '123'
];

// Send purchase request
$response = $gateway->purchase([
    'amount'        => '10.00',
    'currency'      => 'USD',
    'transactionId' => '1234',
    'card'          => $formData
])->send();

// Process response
if ( $response->isSuccessful() )
{
    // Payment was successful
    print_r($response);
}
else
{
    // Payment failed
    echo $response->getMessage();
}
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/Strikewood/omnipay-first-atlantic-commerce/issues),
or better yet, fork the library and submit a pull request.
