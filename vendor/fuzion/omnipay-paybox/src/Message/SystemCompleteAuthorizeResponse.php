<?php

namespace Omnipay\Paybox\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Paybox Complete Authorize Response
 */
class SystemCompleteAuthorizeResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return isset($this->data['Erreur']) && '00000' === $this->data['Erreur'];
    }

    public function getTransactionReference()
    {
        return isset($this->data['Id']) ? $this->data['Id'] : null;
    }

    public function getMessage()
    {
        return !$this->isSuccessful() ? 'Transaction failed' : null;
    }
}
