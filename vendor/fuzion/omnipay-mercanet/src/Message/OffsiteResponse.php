<?php
namespace Omnipay\Mercanet\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Response
 */
class OffsiteResponse extends AbstractResponse implements RedirectResponseInterface
{
  /**
   * endpoint is the remote url - should be provided by the processor.
   * we are using github as a filler
   *
   * @var string
   */
    protected $endpoint;

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
        $this->endpoint = ($request->getEndPoint());
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->data['secret_key'];
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
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->endpoint;
    }

    /**
     * Should the browser redirect using GET or POST
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        $allFields = $this->getData();
        $formData = array();
        foreach ($allFields['data'] as $field => $value) {
            $formData[] = "{$field}={$value}";
        }
        $data = implode('|', $formData);
        return array(
            'data' => $data,
            'InterfaceVersion' => 'HP_2.18',
            'seal' => hash('sha256', $data . $this->getSecretKey()),
        );
    }
}
