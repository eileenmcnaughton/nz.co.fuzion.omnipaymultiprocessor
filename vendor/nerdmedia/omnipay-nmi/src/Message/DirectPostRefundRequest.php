<?php
namespace Omnipay\NMI\Message;

/**
* NMI Direct Post Refund Request
*/
class DirectPostRefundRequest extends DirectPostCaptureRequest
{
    protected $type = 'refund';
}
