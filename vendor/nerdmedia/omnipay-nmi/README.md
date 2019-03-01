# Omnipay: NMI (Network Merchants Inc.)

**NMI (Network Merchants Inc.) driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements [NMI](https://www.nmi.com/) (Network Merchants Inc.) support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply require `nerdmedia/omnipay-nmi` with Composer:

```
composer require nerdmedia/omnipay-nmi:"3.x@dev"
```

## Basic Usage

The following gateways are provided by this package:

* NMI_DirectPost

```php
$gateway = Omnipay::create('NMI_DirectPost');
$gateway->setUsername('demo');
$gateway->setPassword('password');

$request = $gateway->purchase([
    'amount' => $amount,
    'card' => [
        'number' => '4111111111111111',
        'expiryMonth' => '10',
        'expiryYear' => '25',
        'cvv' => '111'
    ],
    ...
])->send();

if ($response->isSuccessful()) {
    // payment was successful: update database
    dump($response);
} else {
    // payment failed: display message to customer
    dump('Error: ' . $response->getMessage());
}
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/thephpleague/omnipay-authorizenet/issues),
or better yet, fork the library and submit a pull request.