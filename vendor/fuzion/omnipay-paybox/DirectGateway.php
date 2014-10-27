<?php
namespace Omnipay\Paybox;

use Omnipay\Common\AbstractGateway;
use Omnipay\PayboxDirect\Message\PurchaseRequest;
use Omnipay\PayboxDirect\Message\RefundRequest;

/**
 * Paybox System Gateway
 *
 * @link http://www1.paybox.com/wp-content/uploads/2014/06/ManuelIntegrationPayboxDirect_V6.3_EN.pdf
 * @link http://www1.paybox.com/wp-content/uploads/2014/02/PayboxTestParameters_V6.2_EN.pdf
 */
class DirectGateway extends SystemGateway
{

    public function getName()
    {
        return 'PayboxDirect';
    }


    /**
     *
     * @param array $parameters
     * @return \Omnipay\PayboxDirect\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayboxDirect\Message\AuthorizeRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\PayboxDirect\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayboxDirect\Message\CaptureRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\PayboxDirect\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayboxDirect\Message\PurchaseRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\PayboxDirect\Message\CompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayboxDirect\Message\CompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\PayboxDirect\Message\CompleteAuthorizeRequest
     */
    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayboxDirect\Message\CompleteAuthorizeRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\PayboxDirect\Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayboxDirect\Message\CreateCardRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\PayboxDirect\Message\UpdateCardRequest
     */
    public function updateCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayboxDirect\Message\UpdateCardRequest', $parameters);
    }

}
