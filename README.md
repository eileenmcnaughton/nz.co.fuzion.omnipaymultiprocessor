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

## Adding support for new payment gateways

* Update `composer.json` with the required Omnipay package for your payment processor
  and run composer update.
* Edit `CRM/Core/Payment/processors.mgd.php`.

### Eway

The gateways supported are:
Rapid Direct - this is the onsite processor
Rapid Shared - re-directs to an eway hosted page
Rapid - transparent redirect, redirects to a form on your site that submits to eway

Note that we always collect Country & Billing name details for
the Shared & transparent redirect variants as it is necessary
for recurring and simpler to always collect them.
