<?php

namespace Omnipay\NMI\Message;

/**
 * NMI Three Step Redirect Sale Request
 */
class ThreeStepRedirectSaleRequest extends ThreeStepRedirectAuthRequest
{
    /**
     * @var string
     */
    protected $type = 'sale';
}
