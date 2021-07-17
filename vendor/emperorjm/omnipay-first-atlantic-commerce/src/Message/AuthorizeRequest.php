<?php

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * FACPG2 Authorize Request
 *
 * Required Parameters:
 * amount - Float ex. "10.00",
 * currency - Currency code ex. "USD",
 * card - Instantiation of Omnipay\FirstAtlanticCommerce\CreditCard
 *
 * There are also 2 optional boolean parameters outside of the normal Omnipay parameters:
 * requireAVSCheck - will tell FAC that we want the to verify the address through AVS
 * createCard - will tell FAC to create a tokenized card in their system while it is authorizing the transaction
 */
class AuthorizeRequest extends AbstractRequest
{
    /**
     * @var string;
     */
    protected $requestName = 'AuthorizeRequest';

    /**
     * Transaction code (flag as a authorization)
     *
     * @var int;
     */
    protected $transactionCode = 0;

    /**
     * Returns the signature for the request.
     *
     * @return string base64 encoded sha1 hash of the merchantPassword, merchantId,
     *    acquirerId, transactionId, amount and currency code.
     */
    protected function generateSignature()
    {
        $signature  = $this->getMerchantPassword();
        $signature .= $this->getMerchantId();
        $signature .= $this->getAcquirerId();
        $signature .= $this->getTransactionId();
        $signature .= $this->formatAmount();
        $signature .= $this->getCurrencyNumeric();

        return base64_encode( sha1($signature, true) );
    }

    /**
     * Validate and construct the data for the request
     *
     * @return array
     */
    public function getData()
    {
        $this->validate('merchantId', 'merchantPassword', 'acquirerId', 'transactionId', 'amount', 'currency', 'card');

        // Check for AVS and require billingAddress1 and billingPostcode
        if ( $this->getRequireAvsCheck() )
        {
            $this->getCard()->validate('billingAddress1', 'billingPostcode');
        }

        // Tokenized cards require the CVV and nothing else, token replaces the card number
        if ( $this->getCardReference() )
        {
            $this->validate('cardReference');
            $this->getCard()->validate('cvv', 'expiryMonth', 'expiryYear');

            $cardDetails = [
                'CardCVV2'       => $this->getCard()->getCvv(),
                'CardExpiryDate' => $this->getCard()->getExpiryDate('my'),
                'CardNumber'     => $this->getCardReference()
            ];
        }
        else
        {
            $this->getCard()->validate();

            $cardDetails = [
                'CardCVV2'       => $this->getCard()->getCvv(),
                'CardExpiryDate' => $this->getCard()->getExpiryDate('my'),
                'CardNumber'     => $this->getCard()->getNumber(),
                'IssueNumber'    => $this->getCard()->getIssueNumber()
            ];
        }

        // Only pass the StartDate if year/month are set otherwise it returns 1299
        if ( $this->getCard()->getStartYear() && $this->getCard()->getStartMonth() )
        {
            $cardDetails['StartDate'] = $this->getCard()->getStartDate('my');
        }

        $transactionDetails = [
            'AcquirerId'       => $this->getAcquirerId(),
            'Amount'           => $this->formatAmount(),
            'Currency'         => $this->getCurrencyNumeric(),
            'CurrencyExponent' => $this->getCurrencyDecimalPlaces(),
            'IPAddress'        => $this->getClientIp(),
            'MerchantId'       => $this->getMerchantId(),
            'OrderNumber'      => $this->getTransactionId(),
            'Signature'        => $this->generateSignature(),
            'SignatureMethod'  => 'SHA1',
            'TransactionCode'  => $this->getTransactionCode()
        ];

        $billingDetails = [
            'BillToAddress'     => $this->getCard()->getAddress1(),
            'BillToZipPostCode' => $this->getCard()->formatPostcode(),
            'BillToFirstName'   => $this->getCard()->getFirstName(),
            'BillToLastName'    => $this->getCard()->getLastName(),
            'BillToCity'        => $this->getCard()->getCity(),
            'BillToCountry'     => $this->getCard()->getNumericCountry(),
            'BillToEmail'       => $this->getCard()->getEmail(),
            'BillToTelephone'   => $this->getCard()->getPhone(),
            'BillToFax'         => $this->getCard()->getFax()
        ];

        // FAC only accepts two digit state abbreviations from the USA
        if ( $billingDetails['BillToCountry'] == 840 )
        {
            $billingDetails['BillToState'] = $this->getCard()->validateState();
        }

        $data = [
            'TransactionDetails' => $transactionDetails,
            'CardDetails'        => $cardDetails,
            'BillingDetails'     => $billingDetails
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
        return parent::getEndpoint() . 'Authorize';
    }

    /**
     * Returns the transaction code based on the AVS check requirement
     *
     * @return int Transaction Code
     */
    protected function getTransactionCode()
    {
        $transactionCode = $this->transactionCode;
        if($this->getRequireAvsCheck()) {
            $transactionCode += 1;
        }
        if($this->getCreateCard()) {
            $transactionCode += 128;
        }
        return $transactionCode;
    }

    /**
     * Return the authorize response object
     *
     * @param \SimpleXMLElement $xml Response xml object
     *
     * @return AuthorizeResponse
     */
    protected function newResponse($xml)
    {
        return new AuthorizeResponse($this, $xml);
    }

    /**
     * @param boolean $value Create a tokenized card on FAC during an authorize request
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setCreateCard($value)
    {
        return $this->setParameter('createCard', $value);
    }

    /**
     * @return boolean Create a tokenized card on FAC during an authorize request
     */
    public function getCreateCard()
    {
        return $this->getParameter('createCard');
    }
}
