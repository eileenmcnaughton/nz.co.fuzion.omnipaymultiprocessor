omnipay-mercanet
=============

Congratulations you have the starting point for your omnipay plugin. Below are some tips
to get it going, but in the end you are the developer & will need to understand & develop it.

**Don't forget to update this readme!**

To create an Omnipay plug from this

1. Create a repo called eileenmcnaughton/omnipay-mercanet on your github account.

2. commit your gateway to git & push to a repo on github. e.g from in your gateway folder:
```
git init
git add .
git commit -m "Initial gateway commit"
git remote add origin git@github.com:eileenmcnaughton/omnipay-mercanet.git
git push origin master
```

3. update the composer.json in your root (ie. the folder above vendor) - add the repository
```
    "repositories": [
           {
               "type": "git",
               "url":  "https://github.com/eileenmcnaughton/omnipay-mercanet.git"
           },
```
4. Add the new require e.g.

```  
      "require":
      {
          "fuzion/omnipay-mercanet": "dev-master"
      },
```


5. Run composer update in your root folder (ie. vendor should be a folder of the folder you are in) using prefer-dist so as to use the files in place ie.

``
composer update fuzion/omnipay-mercanet --prefer-dist
``

6.  Run the unit tests. You should not proceed further until the tests pass. You will need to run composer install from within your
gateway to get the vendor directory there. The tests use phpunit. As long as you are in your gateway directory
you simply need to run

```
phpunit
```

If you are using phpstorm you can run the tests from within the IDE - to configure go to file/settings/php/phpunit
ensure that custom autoloader is selected & the path is set to the phpunit file in your root - e.g

{path}\vendor\phpunit\phpunit\phpunit.php

You can then right-click on the test & choose 'run' or even better 'run with coverage'

10. sign your site up to travis https://travis-ci.org/ and push your extension to github. Once you have done your first build you are ready to start developing your plugin

11. Update your readme

**Writing your plugin**

The shell extension supports an Offsite payment gateway. It is fairly common for gateways to support 2 methods so we
have prepended 'Offsite' to allow space for a second type (we might get more nuanced about this later).

Note that Omnipay does not think of processors as having on-site & off-site distinctions. The point is to provide a model for 2 types in one package and to demonstrate the
functions that are specific to processors that use callbacks - ie IPNs/ Silent Posts / other http or api calls.

1. Set up your payment classes. Generally you should start with the 'AuthorizeRequest', 'PurchaseRequest' & CaptureRequest classes. It is likely there
will be very little difference between the 3 and in the shell extension the PurchaseRequest & CaptureRequest extend the AuthorizeRequest, declaring only a different
transaction type. Start by looking at the following functions 

  - sendData - is the browser is to be redirected the function should look like
      ```
      function sendData($data) {
            return $this->response = new OffsiteAuthorizeResponse($this, $data, $this->getEndpoint());
      }
      ```

      If the server is to communicate directly with the other site then the sendData function encapsulates this 
      interaction

      ```
      function sendData($data) {
           $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $data)->send();
           return $this->response = new OffsiteResponse($this, $httpResponse->getBody());
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

**Fix up your readme**

Once you have registered with packagist and travis you should be able to get the images below showing passes.

[![Build Status](https://travis-ci.org/eileenmcnaughton/omnipay-mercanet.png?branch=master)](https://travis-ci.org/eileenmcnaughton/omnipay-mercanet)
[![Latest Stable Version](https://poser.pugx.org/eileenmcnaughton/omnipay-mercanet/version.png)](https://packagist.org/eileenmcnaughton/omnipay-mercanet)
[![Total Downloads](https://poser.pugx.org/eileenmcnaughton/eileenmcnaughton/omnipay-mercanet/d/total.png)](https://packagist.org/eileenmcnaughton/eileenmcnaughton/omnipay-mercanet)

## Basic Usage

The following gateways are provided by this package:

* Mercanet_Offsite

## Troubleshooting

An incorrect currency will return an error like: 

Technical problem  : code=03 message=None of the merchant's payment means is compliant with the transaction context

 [c06920cdbc57be]