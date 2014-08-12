<?php

namespace Omnipay\Cybersource\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;


/**
 * Stripe Response
 */
class Response extends AbstractResponse implements RedirectResponseInterface
{
    public function __construct(RequestInterface $request, $data, $redirectUrl)
    {
        $this->request = $request;
        $this->data = $data;
        $this->redirectUrl = $redirectUrl;
    }

    public function isSuccessful()
    {
        return FALSE;
    }

    public function isRedirect()
    {
        return TRUE;
    }

    public function isTransparentRedirect()
    {
      return TRUE;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->getData();
    }

    public function getRedirectResponseHiddenFields() {
      $hiddenFields = '';
      foreach ($this->getRedirectData() as $key => $value) {
        $hiddenFields .= sprintf(
            '<input type="hidden" name="%1$s" value="%2$s" />',
            htmlentities($key, ENT_QUOTES, 'UTF-8', false),
            htmlentities($value, ENT_QUOTES, 'UTF-8', false)
          )."\n";
      }
      return $hiddenFields;
    }
}
