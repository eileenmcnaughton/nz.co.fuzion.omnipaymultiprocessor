<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

use Omnipay\FirstAtlanticCommerce\Constants;

class HostedPagePreprocessResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        if ($this->getCode() === "0") return true;

        return false;
    }

    public function getMessage()
    {
        return $this->queryData("ResponseCodeDescription");
    }

    public function getCode()
    {
        return $this->queryData("ResponseCode");
    }

    public function getSecurityToken()
    {
        return $this->queryData("SecurityToken");
    }

    public function verifySignature()
    {
        return $this;
    }

    public function getHostedPageURL()
    {
        if ($this->isSuccessful())
        {
            $pageSetNameAndToken = $this->request->getHostedPagePageSet()."/".$this->request->getHostedPageName()."/".$this->getSecurityToken();
            return ($this->request->getTestMode()) ? Constants::PLATFORM_MERCHANT_PAGES_UAT.$pageSetNameAndToken : Constants::PLATFORM_MERCHANT_PAGES_PROD.$pageSetNameAndToken;
        }

        return null;
    }

    public function redirectToHostedPage()
    {
        header("Location: ".$this->getHostedPageURL());
        exit;
    }
}