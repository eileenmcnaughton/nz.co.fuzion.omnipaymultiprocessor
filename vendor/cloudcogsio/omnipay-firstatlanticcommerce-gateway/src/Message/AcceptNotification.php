<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\FirstAtlanticCommerce\Support\ThreeDSResponse;

class AcceptNotification extends AbstractRequest
implements \Omnipay\Common\Message\NotificationInterface
{    
    public function send()
    {
        return $this->sendData($_POST);
    }
    
    public function sendData($data)
    {
        return new ThreeDSResponse($this->getParameter("FacPwd"), $data, ((!$this->getParameter("FacPwd"))? false : true));
    }
    
    public function setFacPwd($value)
    {
        return $this->setParameter("FacPwd", $value);
    }
    
    public function getFacPwd()
    {
        return $this->getParameter("FacPwd");
    }
    
    public function setMerID($value)
    {
        return $this->setParameter("MerId", $value);
    }
    
    public function setAcqID($value)
    {
        return $this->setParameter("AcqId", $value);
    }
    
    public function setOrderID($value)
    {
        return $this->setParameter("OrderId", $value);
    }
    
    public function setResponseCode($value)
    {
        return $this->setParameter("ResponseCode", $value);
    }
    
    public function getResponseCode()
    {
        return $this->getParameter("ResponseCode");
    }
    
    public function setReasonCode($value)
    {
        return $this->setParameter("ReasonCode", $value);
    }
    
    public function setReasonCodeDesc($value)
    {
        return $this->setParameter("ReasonCodeDesc", $value);
    }
    
    public function getReasonCodeDesc()
    {
        return $this->getParameter("ReasonCodeDesc");
    }
    
    public function setReferenceNo($value)
    {
        return $this->setParameter("ReferenceNo", $value);
    }
    
    public function setPaddedCardNo($value)
    {
        return $this->setParameter("PaddedCardNo", $value);
    }
    
    public function setAuthCode($value)
    {
        return $this->setParameter("AuthCode", $value);
    }
    
    public function setCvv2Result($value)
    {
        return $this->setParameter("CVV2Result", $value);
    }
    
    public function setOriginalResponseCode($value)
    {
        return $this->setParameter("OriginalResponseCode", $value);
    }
    
    public function setSignature($value)
    {
        return $this->setParameter("Signature", $value);
    }
    
    public function setSignatureMethod($value)
    {
        return $this->setParameter("SignatureMethod", $value);
    }
    
    public function getTransactionId()
    {
        return $this->getParameter("OrderId");
    }
    
    public function getData()
    {
        return $this->getParameters();
    }
    
    /**
     * Gateway Reference
     *
     * @return string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->getParameter("ReferenceNo");
    }
    
    /**
     * Was the transaction successful?
     *
     * @return string Transaction status, one of {@link NotificationInterface::STATUS_COMPLETED},
     * {@link NotificationInterface::STATUS_PENDING}, or {@link NotificationInterface::STATUS_FAILED}.
     */
    public function getTransactionStatus()
    {
        if (intval($this->getResponseCode()) === 1) return self::STATUS_COMPLETED;
        
        return self::STATUS_FAILED;
    }
    
    /**
     * Response Message
     *
     * @return string A response message from the payment gateway
     */
    public function getMessage()
    {
        return $this->getReasonCodeDesc();
    }
}