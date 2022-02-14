<?php
namespace Omnipay\FirstAtlanticCommerce\Message;

use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

class Authorize3DSResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * Returns false since transaction has not been processed at this point.
     * Implementations should call $this->redirect() to start off the 3DS flow using the form returned by FAC.
     * 
     * {@inheritDoc}
     * @see \Omnipay\Common\Message\ResponseInterface::isSuccessful()
     */
    public function isSuccessful()
    {
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

    public function getHTMLFormData()
    {
        return $this->queryData("HTMLFormData");
    }

    public function getTokenizedPAN()
    {
        return $this->queryData("TokenizedPAN");
    }

    public function verifySignature()
    {
        return $this;
    }

    /**
     * To be removed. 
     * Use redirect() method instead.
     * 
     * @deprecated
     */
    public function renderHTMLFormData()
    {
        header("Content-Type: text/html");
        print html_entity_decode($this->getHTMLFormData());
        exit;
    }
    
    public function isRedirect()
    {
        return true;
    }
    
    public function getRedirectResponse()
    {
        $this->validateRedirect();
        return new HttpResponse(html_entity_decode($this->getHTMLFormData()));
    }
    
    public function getRedirectMethod()
    {
        return "POST";
    }
    
    /**
     * Shouldn't be empty. Returns a non-empty string so it passes the base class redirect validation.
     * A complete HTML form is returned from FAC which handles the redirect.
     * A redirect URL is not required.
     * 
     * {@inheritDoc}
     * @see \Omnipay\Common\Message\AbstractResponse::getRedirectUrl()
     */
    public function getRedirectUrl()
    {
        return "#";
    }
    
    public function redirect()
    {
        parent::redirect();
        exit;
    }
    
    public function getRedirectData()
    {
        return parent::getRedirectData();
    }
}