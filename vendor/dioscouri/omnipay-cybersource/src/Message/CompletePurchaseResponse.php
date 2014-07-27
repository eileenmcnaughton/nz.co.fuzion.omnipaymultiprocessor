<?php

namespace Omnipay\Cybersource\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * New Complete Purchase response
 */
class CompletePurchaseResponse extends AbstractResponse
{
  public function isSuccessful()
  {
    return isset($this->data['decision']) && 'ACCEPT' === $this->data['decision'];
  }

  public function getTransactionReference()
  {
    return isset($this->data['x_trans_id']) ? $this->data['x_trans_id'] : null;
  }

  public function getMessage()
  {
    return isset($this->data['message']) ? $this->data['message'] : null;
  }
}
