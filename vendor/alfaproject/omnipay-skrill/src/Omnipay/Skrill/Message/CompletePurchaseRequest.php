<?php
namespace Omnipay\Skrill\Message;

use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\Message\AbstractRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Callback handler
 *
 * @package Omnipay\Skrill\Message
 */
class CompletePurchaseRequest extends AbstractRequest
{
    /**
     * Initialize the object with parameters.
     *
     * If any unknown parameters passed, they will be ignored.
     *
     * @param array $parameters An associative array of parameters
     *
     * @return $this
     * @throws RuntimeException
     */
    public function initialize(array $parameters = [])
    {
        if (null !== $this->response) {
            throw new RuntimeException('Request cannot be modified after it has been sent!');
        }

        $this->parameters = new ParameterBag($parameters);

        return $this;
    }

    /**
     * Get the data for this request.
     *
     * @return array request data
     */
    public function getData()
    {
        return $this->parameters->all();
    }

    /**
     * @param  array $data payment data to send
     *
     * @return PaymentResponse         payment response
     */
    public function sendData($data)
    {
        return $this->response = new StatusCallback($data);
    }
}
