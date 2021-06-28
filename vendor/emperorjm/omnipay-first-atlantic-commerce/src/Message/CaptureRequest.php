<?php

namespace Omnipay\FirstAtlanticCommerce\Message;

/**
 * FACPG2 Capture Request
 *
 * Required Parameters:
 * transactionId - Corresponds to the merchant's transaction ID
 * amount - eg. "10.00"
 */
class CaptureRequest extends AbstractTransactionModificationRequest
{
    /**
     * Flag as a capture
     *
     * @var int;
     */
    protected $modificationType = 1;
}
