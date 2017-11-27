<?php
namespace Omnipay\pergo\Message;

/**
 * Complete purchase response.
 *
 * This is the action taken when an IPN, webhook or other callback comes in
 * from the payment gateway provider.
 */
class OffsiteCompletePurchaseResponse extends OffsiteCompleteAuthorizeRequest
{
}
