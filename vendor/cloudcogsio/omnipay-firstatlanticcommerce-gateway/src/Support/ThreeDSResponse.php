<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Support;

use Omnipay\FirstAtlanticCommerce\Constants;
use Omnipay\FirstAtlanticCommerce\Exception\InvalidResponseData;
use Omnipay\Common\Message\AbstractResponse;

/**
 * Response class returned to $gateway->acceptNotification()
 * 
 * Initially implemented here as a Support class but (should) be moved to /Message in future.
 */
class ThreeDSResponse extends AbstractResponse
{
    const ROOT_ELEMENT = "ThreeDSAuthorizeResponse";

    protected $post;
    protected $XMLDoc;

    public function __construct($FacPwd, array $post, $verifySignature = true)
    {
        $this->post = $post;
        $this->FacPwd = $FacPwd;
        $this->createNewXMLDoc();
        $this->XMLDoc->registerXPathNamespace("fac", Constants::PLATFORM_XML_NS);

        if ($this->isSuccessful())
        {
            if ($verifySignature)
                $this->verifySignature();
        }
    }

    public function getData() : \SimpleXMLElement
    {
        return $this->XMLDoc;
    }

    public function isSuccessful()
    {
        return (intval($this->getResponseCode()) === 1);
    }

    public function getMerID()
    {
        return $this->queryData("MerID");
    }

    public function getAcqID()
    {
        return $this->queryData("AcqID");
    }

    public function getOrderID()
    {
        return $this->queryData("OrderID");
    }

    public function getResponseCode()
    {
        return $this->queryData("ResponseCode");
    }

    public function getReasonCode()
    {
        return $this->queryData("ReasonCode");
    }

    public function getReasonCodeDesc()
    {
        return $this->queryData("ReasonCodeDesc");
    }

    public function getTokenizedPAN()
    {
        return $this->queryData("TokenizedPAN");
    }

    public function getReferenceNo()
    {
        return $this->queryData("ReferenceNo");
    }

    public function getPaddedCardNo()
    {
        return $this->queryData("PaddedCardNo");
    }

    public function getAuthCode()
    {
        return $this->queryData("AuthCode");
    }

    public function getCVV2Result()
    {
        return $this->queryData("CVV2Result");
    }

    public function getOriginalResponseCode()
    {
        return $this->queryData("OriginalResponseCode");
    }

    public function getTransactionStain()
    {
        return $this->queryData("TransactionStain");
    }

    public function getECIIndicator()
    {
        return $this->queryData("ECIIndicator");
    }

    public function getAuthenticationResult()
    {
        return $this->queryData("AuthenticationResult");
    }

    public function getCAVV()
    {
        return $this->queryData("CAVV");
    }

    public function getSignature()
    {
        return $this->queryData("Signature");
    }

    public function getSignatureMethod()
    {
        return $this->queryData("SignatureMethod");
    }

    public function verifySignature()
    {
        $FACSignature= $this->getSignature();
        $ValidatedSignature = base64_encode(sha1($this->FacPwd.$this->getMerID().$this->getAcqID().$this->getOrderID(),true));
        if ($FACSignature !== $ValidatedSignature)
            throw new InvalidResponseData("Signature mismatch. Expected: ".$ValidatedSignature);

            return $this;
    }

    public function getMessage()
    {
        return $this->getReasonCodeDesc();
    }

    public function getCode()
    {
        return $this->getResponseCode();
    }

    public function getTransactionReference()
    {
        return $this->getReferenceNo();
    }

    public function getTransactionId()
    {
        return $this->getOrderID();
    }

    public function getExpiryMonth()
    {
        return $this->queryData("ExpiryMonth");
    }

    public function getExpiryYear()
    {
        return $this->queryData("ExpiryYear");
    }

    protected function createNewXMLDoc()
    {
        $rootElement = self::ROOT_ELEMENT;
        $this->XMLDoc = new \SimpleXMLElement("<".$rootElement." xmlns=\"".Constants::PLATFORM_XML_NS."\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" />");

        $this->createXMLFromData($this->XMLDoc, $this->post);
    }

    protected function createXMLFromData(\SimpleXMLElement $parent, $data)
    {
        foreach ($data as $elementName=>$value)
        {
            if (is_array($value))
            {
                $element = $parent->addChild($elementName);
                $this->createXMLFromData($element, $value);
            }
            else
            {
                $parent->addChild($elementName, $value);
            }
        }
    }

    protected function queryData($element)
    {
        $result = $this->getData()->xpath("//fac:$element");
            if (is_array($result) && count($result) > 0)
                return (string) $result[0];

        return null;
    }
}
