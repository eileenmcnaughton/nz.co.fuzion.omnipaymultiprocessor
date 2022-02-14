<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\AbstractResponse as OmnipayAbstractResponse;
use Omnipay\FirstAtlanticCommerce\Exception\InvalidResponseData;
use Omnipay\FirstAtlanticCommerce\Constants;

abstract class AbstractResponse extends OmnipayAbstractResponse
{
    const AUTHORIZE_CREDIT_CARD_TRANSACTION_RESULTS = "CreditCardTransactionResults";
    const AUTHORIZE_BILLING_DETAILS = "BillingDetails";
    const AUTHORIZE_FRAUD_CONTROL_RESULTS = "FraudControlResults";

    public function __construct(RequestInterface $request, $data)
    {
        if ($data instanceof \SimpleXMLElement)
        {
            $this->request = $request;
            $this->data = $data;

            parent::__construct($request, $data);

            if(intval($this->queryData("ResponseCode")) === 1)
                $this->verifySignature();
        }
        else
        {
            throw new InvalidResponseData("Response data is not valid XML");
        }
    }

    public function getRequest() : AbstractRequest
    {
        return $this->request;
    }

    public function getData() : \SimpleXMLElement
    {
        return $this->data;
    }

    protected function queryData($element, $parent = null)
    {
        if($parent == null)
        {
            $result = $this->getData()->xpath("//fac:$element");
            if (is_array($result) && count($result) > 0)
                return (string) $result[0];
        }
        else
        {
            switch ($parent)
            {
                case self::AUTHORIZE_CREDIT_CARD_TRANSACTION_RESULTS:
                    $parentElement = $this->getData()->xpath("//fac:CreditCardTransactionResults");

                    break;

                case self::AUTHORIZE_BILLING_DETAILS:
                    $parentElement = $this->getData()->xpath("//fac:BillingDetails");
                    break;

                case self::AUTHORIZE_FRAUD_CONTROL_RESULTS:
                    $parentElement = $this->getData()->xpath("//fac:FraudControlResults");
                    break;

                default:
                    return null;
                    break;
            }

            if (!empty($parentElement) && ($parentElement[0] instanceof \SimpleXMLElement))
            {
                $parentElement[0]->registerXPathNamespace("fac", Constants::PLATFORM_XML_NS);
                $result = $parentElement[0]->xpath("fac:$element");
                if (is_array($result) && count($result) > 0)
                    return (string) $result[0];
            }

        }

        return null;
    }

    abstract public function verifySignature();
}