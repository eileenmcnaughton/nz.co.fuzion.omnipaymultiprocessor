<?php
/**
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Omnipay\FirstAtlanticCommerce\Message;

/**
 * Hosted Page Integration requires configuration on FAC's merchant portal.
 * 
 * Hosted Page Set and Hosted Page Name must be setup in FAC and configured when using the Hosted Page option for "authorize()" or "purchase()"
 * 
 *   setHostedPagePageSet($pageSet)
 *   setHostedPageName($pageName)
 * 
 * Ex. 
 * $HostedPagePreprocess = $gateway->purchase([Constants::AUTHORIZE_OPTION_HOSTED_PAGE => true];
 * $HostedPagePreprocess
 *  ->setHostedPagePageSet($pageSet)
 *  ->setHostedPageName($pageName)
 *  ->setCardHolderResponseURL($URL) // Page on the Merchant’s site that will process the response code returned and present a suitable message to the Cardholder
 *  ->send();
 */
class HostedPagePreprocess extends Authorize
{
    const MESSAGE_PART_CARDHOLDER_RESPONSE_URL = 'cardHolderResponseURL';
    const HOSTED_PAGE_PAGESET = "hostedPagePageSet";
    const HOSTED_PAGE_NAME = "hostedPageName";

    public function getData()
    {
        $this->TransactionDetails = array_merge($this->TransactionDetails, $this->setTransactionDetailsCommon());
        $this->setTransactionDetails();

        $this->applyCardHolderResponseURL();

        return $this->data;
    }

    public function setCardHolderResponseURL($url)
    {
        return $this->setParameter(self::MESSAGE_PART_CARDHOLDER_RESPONSE_URL, $url);
    }

    public function getCardHolderResponseURL()
    {
        return $this->getParameter(self::MESSAGE_PART_CARDHOLDER_RESPONSE_URL);
    }

    public function setHostedPagePageSet($pageSet)
    {
        return $this->setParameter(self::HOSTED_PAGE_PAGESET, $pageSet);
    }

    public function getHostedPagePageSet()
    {
        return $this->getParameter(self::HOSTED_PAGE_PAGESET);
    }

    public function setHostedPageName($pageName)
    {
        return $this->setParameter(self::HOSTED_PAGE_NAME, $pageName);
    }

    public function getHostedPageName()
    {
        return $this->getParameter(self::HOSTED_PAGE_NAME);
    }

    protected function applyCardHolderResponseURL()
    {
        $this->data[ucfirst(self::MESSAGE_PART_CARDHOLDER_RESPONSE_URL)] = $this->getCardHolderResponseURL();
    }
}