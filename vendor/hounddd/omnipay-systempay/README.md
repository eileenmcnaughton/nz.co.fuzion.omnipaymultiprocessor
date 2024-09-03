# Omnipay: SystemPay

**SystemPay driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.com/Hounddd/omnipay-systempay.svg?branch=master)](https://travis-ci.com/hounddd/omnipay-systempay)
[![Latest Stable Version](https://poser.pugx.org/hounddd/omnipay-systempay/version.png)](https://packagist.org/packages/hounddd/omnipay-systempay)
[![Total Downloads](https://poser.pugx.org/hounddd/omnipay-systempay/d/total.png)](https://packagist.org/packages/hounddd/omnipay-systempay)

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements SystemPay support for Omnipay.

SystemPay is the technical platform for the following digital payment solutions: 
- Cyberplus Paiement (Banque Populaire)
- Paiement Express (Banque Populaire)
- SP Plus (Caisse d'Épargne)

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "hounddd/omnipay-systempay": "dev-master"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* SystemPay

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/omnipay/securepay/issues),
or better yet, fork the library and submit a pull request.
