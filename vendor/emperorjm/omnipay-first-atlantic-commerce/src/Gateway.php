<?php

namespace Omnipay\FirstAtlanticCommerce;

use Omnipay\Common\AbstractGateway;

/**
 * First Atlantic Commerce Payment Gateway 2 (XML POST Service)
 */
class Gateway extends AbstractGateway
{
    use ParameterTrait;

    /**
     * @return string Gateway name.
     */
    public function getName()
    {
        return 'First Atlantic Commerce Payment Gateway 2';
    }

    /**
     * @return array Default parameters.
     */
    public function getDefaultParameters()
    {
        return [
            'merchantId'       => null,
            'merchantPassword' => null,
            'acquirerId'       => '464748',
            'testMode'         => false,
            'requireAvsCheck'  => true
        ];
    }

    /**
     * Authorize an amount on the customer’s card.
     *
     * @param array $parameters
     *
     * @return \Omnipay\FirstAtlanticCommerce\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\FirstAtlanticCommerce\Message\AuthorizeRequest', $parameters);
    }

    /**
     * Capture an amount you have previously authorized.
     *
     * @param array $parameters
     *
     * @return \Omnipay\FirstAtlanticCommerce\Message\CaptureRequest
     */
    public function capture(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\FirstAtlanticCommerce\Message\CaptureRequest', $parameters);
    }

    /**
     *  Authorize and immediately capture an amount on the customer’s card.
     *
     * @param array $parameters
     *
     * @return \Omnipay\FirstAtlanticCommerce\Message\PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\FirstAtlanticCommerce\Message\PurchaseRequest', $parameters);
    }

    /**
     *  Refund an already processed transaction.
     *
     * @param array $parameters
     *
     * @return \Omnipay\FirstAtlanticCommerce\Message\RefundRequest
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\FirstAtlanticCommerce\Message\RefundRequest', $parameters);
    }

    /**
     *  Reverse an already submitted transaction that hasn't been settled.
     *
     * @param array $parameters
     *
     * @return \Omnipay\FirstAtlanticCommerce\Message\VoidRequest
     */
    public function void(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\FirstAtlanticCommerce\Message\VoidRequest', $parameters);
    }

    /**
     *  Retrieve the status of any previous transaction.
     *
     * @param array $parameters
     *
     * @return \Omnipay\FirstAtlanticCommerce\Message\StatusRequest
     */
    public function status(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\FirstAtlanticCommerce\Message\StatusRequest', $parameters);
    }

    /**
     *  Create a stored card and return the reference token for future transactions.
     *
     * @param array $parameters
     *
     * @return \Omnipay\FirstAtlanticCommerce\Message\CreateCardRequest
     */
    public function createCard(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\FirstAtlanticCommerce\Message\CreateCardRequest', $parameters);
    }

    /**
     *  Update a stored card.
     *
     * @param array $parameters
     *
     * @return \Omnipay\FirstAtlanticCommerce\Message\UpdateCardRequest
     */
    public function updateCard(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\FirstAtlanticCommerce\Message\UpdateCardRequest', $parameters);
    }
}
