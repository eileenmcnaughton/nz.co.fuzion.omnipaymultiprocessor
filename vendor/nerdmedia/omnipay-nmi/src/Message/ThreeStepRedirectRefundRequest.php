<?php

namespace Omnipay\NMI\Message;

/**
 * NMI Three Step Redirect Refund Request
 */
class ThreeStepRedirectRefundRequest extends ThreeStepRedirectCaptureRequest
{
    /**
     * @var string
     */
    protected $type = 'refund';
}
