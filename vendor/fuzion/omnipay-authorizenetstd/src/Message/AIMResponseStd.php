<?php

namespace Omnipay\AuthorizeNetStd\Message;

use Omnipay\AuthorizeNet\Message\AIMResponse;

/**
 * Authorize.Net AIM Response
 */
class AIMResponseStd extends AIMResponse
{
  /**
   * Get transaction reference.
   *
   * The whole point of this gateway is to override this function
   * to return the actual reference.
   *
   * @param bool $serialize Determines whether a string or object is returned
   * @return string
   */
  public function getTransactionReference($serialize = true)
  {
    // The transactionResponse is only returned if successful or declined
    // for some reason, so don't assume it will always be there.

    if (isset($this->data->transactionResponse[0])) {
      $body = $this->data->transactionResponse[0];
      // Just return the damn reference!
      // Authorize.net requires the expiry date & last 4 card numbers so omnipay has kinda
      // hacked around that by returning a json reference including all 3. We want auditability so that's no
      // good.
      return (string) $body->transId;
    }

    return '';
  }
}
