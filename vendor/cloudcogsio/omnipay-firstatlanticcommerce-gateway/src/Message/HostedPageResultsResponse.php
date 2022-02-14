<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\FirstAtlanticCommerce\Exception\InvalidResponseData;

class HostedPageResultsResponse extends AuthorizeResponse
{
    public function verifySignature()
    {
        $FACSignature= $this->getSignature();
        $ValidatedSignature = base64_encode(sha1($this->getRequest()->getFacPwd().$this->getRequest()->getFacId().$this->getRequest()->getFacAcquirer().$this->getOrderNumber(),true));
        if ($FACSignature !== $ValidatedSignature)
            throw new InvalidResponseData("Signature mismatch");

            return $this;
    }

    public function getOrderNumber()
    {
        return $this->queryData('OrderNumber');
    }
}