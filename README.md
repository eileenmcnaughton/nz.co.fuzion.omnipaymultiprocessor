# CiviCRM OmniPay Multiprocessor

This extension provides support for multiple payment processors in CiviCRM.

## Supported processors

The following payment processors are supported:

* [Cybersource](../docs/Cybersource.md)
* Paybox System
* GoPay
* Mollie
* Payment Express - PXPay, PxFusion
* NAB Transact
* Eway RapidDirect, Rapid & Shared
* PayPal - Standard, Pro, REST & Express
* Authorize AIM
* Mercanet

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
* Edit `CRM/Core/Payment/processors.mgd.php`.

### Eway

The gateways supported are:
* Rapid Direct - this is the onsite processor
* Rapid Shared - re-directs to an eway hosted page
* Rapid - transparent redirect, redirects to a form on your site that submits to eway

Note that we always collect Country & Billing name details for
the Shared & transparent redirect variants as it is necessary
for recurring and simpler to always collect them.
