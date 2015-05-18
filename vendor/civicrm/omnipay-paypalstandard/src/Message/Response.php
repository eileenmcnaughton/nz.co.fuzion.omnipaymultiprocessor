<?php

namespace Omnipay\Paypalstandard\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Response
 */
class Response extends AbstractResponse implements RedirectResponseInterface
{
  /**
   * endpoint is the remote url - should be provided by the processor.
   * we are using github as a filler
   *
   * @var string
   */

    // LIVE
    //public $endpoint = 'https://www.paypal.com/cgi-bin/webscr';
    public $endpoint = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    // protected $endpoint;

  /**
   * Get end point.
   *
   * @return string
   */
  //public function getEndpoint()
 // {
   // return $this->endpoint;
 // }

  /**
   * Set end point.
   *
   * @param string $endpoint
   *   Set URL to redirect to.
   */
  //public function setEndpoint($endpoint)
  //{
  //  $this->endpoint = $endpoint;
  //}

  public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    /**
     * Has the call to the processor succeeded?
     * When we need to redirect the browser we return false as the transaction is not yet complete
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * Should the user's browser be redirected?
     *
     * @return bool
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * Transparent redirect is the mode whereby a form is presented to the user that POSTs to the payment
     * processor site directly. If this returns true the site will need to provide a form for this
     *
     * @return bool
     */
    public function isTransparentRedirect()
    {
        return false;
    }

    public function getRedirectUrl()
    {
      $testURL = $this->endpoint .'?' . http_build_query($this->data) . '&bn=CiviCRM_SP'. '&cmd=_xclick';
      return $this->endpoint .'?' . http_build_query($this->data) . '&bn=CiviCRM_SP'. '&cmd=_xclick';
    }

    /**
     * Should the browser redirect using GET or POST
     * @return string
     */
    public function getRedirectMethod()
    {
        // KG
        // could set to POST
        return 'GET';
    }

    public function getRedirectData()
    {
        return $this->getData();
    }
}
