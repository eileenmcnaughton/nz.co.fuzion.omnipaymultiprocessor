<?php
namespace Omnipay\Pergo\Message;

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
