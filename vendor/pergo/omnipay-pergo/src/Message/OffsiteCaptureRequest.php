<?php
namespace Omnipay\pergo\Message;

/**
 * Capture Request
 */
class OffsiteCaptureRequest extends OffsiteAuthorizeRequest
{
    public function getTransactionType()
    {
        return 'capture';
    }
}
