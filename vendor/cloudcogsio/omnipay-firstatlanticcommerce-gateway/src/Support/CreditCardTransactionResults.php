<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

use Omnipay\FirstAtlanticCommerce\Message\AbstractResponse;

class CreditCardTransactionResults extends AbstractResults
{
    public function getAuthCode()
    {
        return $this->queryData("AuthCode", AbstractResponse::AUTHORIZE_CREDIT_CARD_TRANSACTION_RESULTS);
    }

    public function getAVSResult()
    {
        return $this->queryData("AVSResult");
    }

    public function getCVV2Result()
    {
        $CVV2ResultCode = $this->queryData("CVV2Result");
        return new CVVResponse($CVV2ResultCode);
    }

    public function getOriginalResponseCode()
    {
        return $this->queryData("OriginalResponseCode");
    }

    public function getPaddedCardNumber()
    {
        return $this->queryData("PaddedCardNumber");
    }

    public function getReasonCode()
    {
        return $this->queryData("ReasonCode", AbstractResponse::AUTHORIZE_CREDIT_CARD_TRANSACTION_RESULTS);
    }

    public function getReasonCodeDescription()
    {
        return $this->queryData("ReasonCodeDescription", AbstractResponse::AUTHORIZE_CREDIT_CARD_TRANSACTION_RESULTS);
    }

    public function getReferenceNumber()
    {
        return $this->queryData("ReferenceNumber");
    }

    public function getResponseCode()
    {
        return $this->queryData("ResponseCode", AbstractResponse::AUTHORIZE_CREDIT_CARD_TRANSACTION_RESULTS);
    }

    public function getTokenizedPAN()
    {
        return $this->queryData("TokenizedPAN");
    }

    public function getFraudScore()
    {
        return $this->queryData("Score");
    }

    public function getFraudControlId()
    {
        return $this->queryData("FraudControlId");
    }

    public function getFraudResponseCode()
    {
        return $this->queryData("FraudResponseCode");
    }

    public function getFraudReasonCode()
    {
        return $this->queryData("ReasonCode", AbstractResponse::AUTHORIZE_FRAUD_CONTROL_RESULTS);
    }

    public function getFraudReasonCodeDesc()
    {
        return $this->queryData("ReasonCodeDesc", AbstractResponse::AUTHORIZE_FRAUD_CONTROL_RESULTS);
    }

    public function getFraudStatusPassFail()
    {
        return $this->queryData("ResponseCode", AbstractResponse::AUTHORIZE_FRAUD_CONTROL_RESULTS);
    }
}