<?php

namespace Omnipay\NMI\Message;

/**
 * NMI Three Step Redirect Complete Request
 */
class ThreeStepRedirectCompleteActionRequest extends ThreeStepRedirectAbstractRequest
{
    /**
     * @var string
     */
    protected $type = 'complete-action';

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('token_id');

        $data = array(
            'api-key'  => $this->getApiKey(),
            'token-id' => $this->getTokenId(),
        );

        return $data;
    }
}
