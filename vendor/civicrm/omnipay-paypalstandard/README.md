# omnipay-shell
=============

**basic file structure to provide a starting point for an Omnipay Gateway plugin**

[![Build Status](https://travis-ci.org/eileenmcnaughton/omnipay-shell.png?branch=master)](https://travis-ci.org/eileenmcnaughton/omnipay-shell)
[![Latest Stable Version](https://poser.pugx.org/eileenmcnaughton/omnipay-shell/version.png)](https://packagist.org/eileenmcnaughton/omnipay-shell/mollie)
[![Total Downloads](https://poser.pugx.org/eileenmcnaughton/omnipay-shell/d/total.png)](https://packagist.org/eileenmcnaughton/omnipay-shell/mollie)

To create an Omnipay plug from this

1. ensure you have a function omnipay/composer environment.
  - install composer (https://getcomposer.org/doc/01-basic-usage.md) (useful tips at http://moquet.net/blog/5-features-about-composer-php/)
  - add the following to your composer.json file
```
      "require":
            {
                "omnipay/omnipay": "~2.0",
            },
```
   - composer update

2. copy & rename changing omnipay-shell to vendor/yourname/omnipay-yourplugin

3. create an appropriate README e.g based on https://github.com/thephpleague/omnipay-mollie/blob/master/README.md - make sure you fix up the build links above

4. update the composer.json file in your plugin directory - looking for references to 'Shell' or 'shell' to replace with your extension
(it's also unlikely that as the author you will have the same name as me so you might need to update that too :-)

5. take a note of your namespace - ie. this line in composer.json
```
(    "autoload": {
        "psr-4": { "Omnipay\\Shell\\" : "src/" }
     },
)
```

and ensure the namespace in all files in the src directory have that namespace (e.g replace Omnipay\Shell with your namespace).
In general doing a find and replace in your src directory on the word Shell should work

6. commit your extension to git & push to a repo on github (am working on the assumption of github)

7. update the composer.json in your root (ie. the folder above vendor) - add the repository
```
    "repositories": [
           {
               "type": "git",
               "url":  "https://github.com/eileenmcnaughton/omnipay-shell.git"
           },
```

  and the new 'require' - instead of the second one here

```  
      "require":
      {
          "omnipay/omnipay": "~2.0",
          "fuzion/omnipay-shell": "dev-master"
      },
```

8. run composer update in your root folder (ie. vendor should be a folder of the folder you are in) using prefer-dist so as to use the files in place ie.
composer update --prefer-dist

9.  run the unit tests. You should not proceed further until the tests pass. The tests use phpunit which you can google. The command will look something like
php vendor/phpunit/phpunit/phpunit.php  Omnipay/Shell/XoffGatewayTest vendor/fuzion/omnipay-shell/tests/XoffGatewayTest.php

If you are using phpstorm you can run the tests from within the IDE - to configure go to file/settings/php/phpunit
ensure that custom autoloader is selected & the path is set to the phpunit file in your root - e.g

{path}\vendor\phpunit\phpunit\phpunit.php

You can then right-click on the test & choose 'run' or even better 'run with coverage'

10. sign your site up to travis https://travis-ci.org/ and push your extension to github. Once you have done your first build you are ready to start developing your plugin


**Writing your plugin**

The shell extension supports 2 Gateways. This is fairly typical - we might see an gateway like paypal supports an off-site processor (paypal standard) and an on-site
processor (paypal pro). If you only need one type you don't need to prefix your filenames/ classes etc. However, if might make sense to prefix them 'in case'.

Normally the prefix would reflect the vendor's naming convention (e.g Standard & Pro for paypal). In the shell extension I am using Xoff and Xon to represent an off-site
 and an on-site processor. These have been selected as being fairly unique for searching and replacing.

Note that Omnipay does not think of processors as having on-site & off-site distinctions. The point is to provide a model for 2 types in one package and to demonstrate the
functions that are specific to processors that use callbacks - ie IPNs/ Silent Posts / other http or api calls.

1. Set up your payment classes. Generally you should start with the 'AuthorizeRequest', 'PurchaseRequest' & CaptureRequest classes. It is likely there
will be very little difference between the 3 and in the shell extension the PurchaseRequest & CaptureRequest extend the AuthorizeRequest, declaring only a different
transaction type. Start by looking at the following functions 

  - sendData - is the browser is to be redirected the function should look like
      ```
      function sendData($data) {
            return $this->response = new XoffAuthorizeResponse($this, $data, $this->getEndpoint());
      }
      ```

      If the server is to communicate directly with the other site then the sendData function encapsulates this 
      interaction

      ```
      function sendData($data) {
           $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $data)->send();
           return $this->response = new AIMResponse($this, $httpResponse->getBody());
      }
      ```


2. Declaring & validating required fields.

There are 2 types of required fields 'card' fields and 'core' fields - the difference is that the core fields are about your site and the transaction
and card fields are about the person. For a list look at https://github.com/thephpleague/omnipay

The shell extension uses functions to declare the required fields and the getData function in the shell 
extension validates these. (It is hoped these functions would also be accessible to the calling app to do pre-validation
Note that you are referencing the normalised Omnipay fields here not the ones defined by the processor

    public function getRequiredCoreFields()
    {
        return array
        (
            'amount',
            'currency',
        );
    }

    public function getRequiredCardFields()
    {
        return array
        (
            'email',
        );
    }

3. getTransactionData()

  This is where you declare the mappings between the omnipay normalised fields and the payment gateway's field
  names as a simple array.

  - to get the amount you can use $this->getAmount() for an amount like '1.00' or getAmountInteger() for an amount like 100
  (for the same amount)
