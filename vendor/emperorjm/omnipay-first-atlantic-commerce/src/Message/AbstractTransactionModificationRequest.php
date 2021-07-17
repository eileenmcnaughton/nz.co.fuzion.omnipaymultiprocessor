<?php

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * FACPG2 Transaction Modification Request
 */
abstract class AbstractTransactionModificationRequest extends AbstractRequest
{
    /**
     * @var string;
     */
    protected $requestName = 'TransactionModificationRequest';

    /**
     * Modification Type
     *
     * @var int;
     */
    protected $modificationType;

    /**
     * Validate and construct the data for the request
     *
     * @return array
     */
    public function getData()
    {
        $this->validate('merchantId', 'merchantPassword', 'acquirerId', 'transactionId', 'amount');

        $data = [
            'AcquirerId'       => $this->getAcquirerId(),
            'Amount'           => $this->formatAmount(),
            'CurrencyExponent' => $this->getCurrencyDecimalPlaces(),
            'MerchantId'       => $this->getMerchantId(),
            'ModificationType' => $this->getModificationType(),
            'OrderNumber'      => $this->getTransactionId(),
            'Password'         => $this->getMerchantPassword()
        ];

        return $data;
    }

    /**
     * Returns endpoint for authorize requests
     *
     * @return string Endpoint URL
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint() . 'TransactionModification';
    }

    /**
     * Returns the modification type
     *
     * @return int Modification Type
     */
    protected function getModificationType()
    {
        return $this->modificationType;
    }

    /**
     * Return the transaction modification response object
     *
     * @param \SimpleXMLElement $xml Response xml object
     *
     * @return ResponseInterface
     */
    protected function newResponse($xml)
    {
        return new TransactionModificationResponse($this, $xml);
    }
}
