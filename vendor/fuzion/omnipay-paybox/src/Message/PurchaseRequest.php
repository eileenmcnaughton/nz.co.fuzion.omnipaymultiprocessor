<?php

namespace Omnipay\Paybox\Message;

/**
 * Cybersource Purchase Request
 */
class PurchaseRequest extends AuthorizeRequest
{
  public function getTransactionType()
  {
    return '00003';
  }
}
