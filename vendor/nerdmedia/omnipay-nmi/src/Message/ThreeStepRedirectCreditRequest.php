<?php

namespace Omnipay\NMI\Message;

/**
 * NMI Three Step Redirect Credit Request
 */
class ThreeStepRedirectCreditRequest extends ThreeStepRedirectAuthRequest
{
    /**
     * @var string
     */
    protected $type = 'credit';
}
