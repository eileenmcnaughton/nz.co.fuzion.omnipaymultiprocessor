This folder defines the PHAR library (`civicrm-omnipay@X.phar`).  Sources are downloaded
via `composer` and moved into an isolated namespace (`CiviOmniPay`).  The resulting
library is tracked as `dist/civicrm-omnipay@X.Y.Z.phar`.

## Prefixing

Packages in this folder use namespace-prefixes. Compare:

* __Original Class__: `\Symfony\Component\Mailer\MailerInterface`
* __Prefixed Class__: `\CiviOmniPay\Symfony\Component\Mailer\MailerInterface`

## Autoloader

To use the PHAR library, register via Pathload:

```php
pathload()->addSearchDir(__DIR__ . '/dist');
pathload()->addNamespace('civicrm-omnipay@6', ['CiviOmniPay\\']);
```

When any classes from `CiviOmniPay\\` are used, the PHAR file is mounted.  Within
the PHAR, we inherit autoloading rules from the `composer.json` of each
nested library.

## Managing packages

To add, remove, or update the packages within `civicrm-omnipay.phar`, you can use
`composer` commands, e.g.

```bash
cd lib/civicrm-omnipay
composer update foo/bar
compsoer remove baz/quux
composer require whiz/bang
```

When you test the new libraries, you may find that you need to fine-tune:

* The list of files/directories/filters (`box.json`). By default, this
  includes most `*.php` files from `vendor/` - but other may require tweaking.
* The namespace-prefixing (`scoper.inc.php`). By default, this skips
  prefixing some top-level packages (like `Psr\*`).

## Building

The `ctrl.sh` script will run `composer` and `box` to produce a suitable PHAR.

> TIP: This will also leave an extra folder called `vendor/` that contains the original files.
> If you use an IDE like PhpStorm, then the folder will create extra noise
> that makes it harder to use auto-completion. You should delete the  leftover `vendor/`

Here is how I typically run it:

```bash
nix-shell --run './lib/civicrm-omnipay/lib.sh build clean'
```
