# Omnipay: Beanstream

**Beanstream payment processing driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/lemonstand/omnipay-beanstream.svg)](https://travis-ci.org/lemonstand/omnipay-beanstream) [![Coverage Status](https://coveralls.io/repos/github/lemonstand/omnipay-beanstream/badge.svg?branch=master)](https://coveralls.io/github/lemonstand/omnipay-beanstream?branch=master) [![Latest Stable Version](https://poser.pugx.org/lemonstand/omnipay-beanstream/v/stable.svg)](https://packagist.org/packages/lemonstand/omnipay-beanstream) [![Total Downloads](https://poser.pugx.org/lemonstand/omnipay-beanstream/downloads)](https://packagist.org/packages/lemonstand/omnipay-beanstream)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Beanstream support for Omnipay. Please see the [Beanstream Developer Portal](http://developer.beanstream.com/) for more information.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "lemonstand/omnipay-beanstream": "~1.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* Beanstream

```php
    $gateway = Omnipay::create('Beanstream');
    $gateway->setMerchantId('[MERCHANT_ID]');
    $gateway->setApiPasscode('[API_PASSCODE]');


    try {
        $params = array(
            'amount'                => 10.00,
            'card'                  => $card,
            'payment_method'        => 'card'
        );

        $response = $gateway->purchase($params)->send();

        if ($response->isSuccessful()) {
            // successful
        } else {
            throw new ApplicationException($response->getMessage());
        }
    } catch (ApplicationException $e) {
        throw new ApplicationException($e->getMessage());
    }

```

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/lemonstand/omnipay-beanstream/issues),
or better yet, fork the library and submit a pull request.
