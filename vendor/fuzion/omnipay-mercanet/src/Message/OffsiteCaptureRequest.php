<?php
namespace Omnipay\Mercanet\Message;

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
