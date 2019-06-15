# CiviCRM OmniPay Multiprocessor

This extension provides support for multiple payment processors in CiviCRM.

## Supported processors

The following payment processors are supported:

* Authorize AIM
* [Cybersource](../docs/Cybersource.md)
* Eway RapidDirect, Rapid & Shared
* GoPay
* Mercanet
* Mollie
* NAB Transact
* Paybox System
* Payment Express - PXPay, PxFusion
* [PayPal Checkout](https://github.com/eileenmcnaughton/nz.co.fuzion.omnipaymultiprocessor/blob/master/docs/Paypal.md)

## Configuration

* Visit **Administer > System Settings > Payment Processors**
* Select the appropriate **Payment Processor Type**

### IPN / Notification URL configuration

If your payment processor requires configuration of an IPN or payment notification URL,
obtain the payment processor ID from the URL when editing the payment processor at
CiviCRM's Administer > System Settings > Payment Processors, then use a URL similar to
`https://example.org/civicrm/payment/ipn/XX` (where `https://example.org` is your actual
site URL and `XX` is the processor ID).

This extension creates `ProcessRecurring` schedule job on installation. It passes all 
due recur contributions and is configured to execute `Hourly`.


## Optional configuration (5.x CiviCRM) - add transaction prefix

We send the contribution id to the payment processor as the transaction identifier.

There may be some risk in some instances of there being a duplication (especially on test accounts).
It is possible to define a prefix that will be prepended to the contribution id one the latest
CiviCRM code. This is only supported by non-UI configuration for now as the code is in the 5.x version. Once it is released I will see about improving configurability.

However, once you have a suitable CiviCRM version you need to 
a) add a payment processor option to cg_extends (civicrm/admin/options?gid=57&reset=1) ie.
value - 'PaymentProcessor'
name - 'civicrm_payment_processor'
title - 'Payment Processors'

b) create a custom data group for payment processors

c) create a field in that custom data group for the value to be prepended. 
IMPORTANT - the name of this field must be Transaction_Prefix

note - Mercanet does not support '-' characters in this prefix. Untested on other processors.

## Adding support for new payment gateways

* Update `composer.json` with the required Omnipay package for your payment processor
  and run composer update.
* Add a new `mgd` file in the `Metadata` directory.

## Developer help
It is possible to log http requests and responses to the civicrm_system_log table using the omnipay_developer_mode setting. This is only appropriate for developers. Output is available on the omnilog report if extended reports are installed.

## Site test mode
It is possible to set the omnipay_test_mode to put all transactions for Omnipay into
test mode. In this case you can use your test credentials on the live processor
and all transactions will be sent to your processor's sandbox site.

### Eway

The gateways supported are:
* Rapid Direct - this is the onsite processor
* Rapid Shared - re-directs to an eway hosted page
* Rapid - transparent redirect, redirects to a form on your site that submits to eway

Note that we always collect Country & Billing name details for
the Shared & transparent redirect variants as it is necessary
for recurring and simpler to always collect them.

##Paypal

Omnipay supports the newer REST library. The Paypal Rest express flow is
1) Paypal Express button presented on form
2) On clicking the button the CiviCRM PaymentProcessor.preapprove
api function is called
3) This function authenticates the CiviCRM site with paypal (using the Omnipay authorize method)and negotiates a
token for the transaction.
4) This is returned to a paypal script (promise).
5) The paypal script opens a popup for the user to approve the transaction
6) pop up closes & user is returned to the page
7) js is used to submit the form
8) after the confirm page the doPayment function calls the payment method. The token
is used to confirm the payment at that point.

Known issues
1) load form is slow when changing processor - oddly same thing not slow on webform - why? We are loading Contribution_Main... slowly?
2) We need to validate the form data before submitting to paypal.


## Code overrides
Currently upstream repos are overridden for the following reasons

#####Paypal Rest

[Overriden to support authorize with card param present but not card](https://github.com/thephpleague/omnipay-paypal/pull/218) & [general jsv4 support](https://github.com/thephpleague/omnipay-paypal/pull/221)

#####Eway
[Overridden to support client side encryption, upstream PR submitted](https://github.com/thephpleague/omnipay-eway/pull/29)

#####Razorpay
[Overridden to support Omnipay 3, upstream PR submitted](https://github.com/razorpay/omnipay-razorpay/pull/7)
