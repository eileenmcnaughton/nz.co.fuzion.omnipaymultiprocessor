This extension provides a wrapper extension for payment processors.

Note that the IPN/ Silent post url that should be configured within the payment processor is

http(s)://yousite/civicrm/payment/ipn/xx where xx is the payment processor ID.

(I'm not quite sure the joomla & WP variants but will update when I do)

How to add new payment gateways to Omnipay


1) update composer.json and run "composer update vendorname/vendorpackage". You can also run "composer update" to update everything.


2) edit CRM/Core/Payment/processors.mgd.php - check notes in that file for syntax

