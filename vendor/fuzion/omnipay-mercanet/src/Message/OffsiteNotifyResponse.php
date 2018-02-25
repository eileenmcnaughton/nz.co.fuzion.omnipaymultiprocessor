<?php

namespace Omnipay\Mercanet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;

/**
 * Sage Pay Server Notification Respons.
 * Return the appropriate response to Sage Pay.
 */
class OffsiteNotifyResponse implements \Omnipay\Common\Message\NotificationInterface
{

    use OffsiteNotificationTrait;

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    /**
     * Live separator for return message to Sage Pay.
     */
    const LINE_SEP = "\r\n";

    /**
     * Whether to exit immediately on responding.
     * For 3.0 it will be worth switching this off by default to
     * provide more control to the application.
     */
    protected $exit_on_response = true;

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Was the transaction successful?
     *
     * @return string Transaction status, one of {@see STATUS_COMPLETED}, {@see #STATUS_PENDING},
     * or {@see #STATUS_FAILED}.
     */
    public function getTransactionStatus()
    {
        // @todo
    }

    /**
     * Construct the response body.
     *
     * @return string
     */
    public function getResponseBody()
    {
        return "Success<p>";
    }

    /**
     * Confirm
     *
     * Notify Mercanet you received the payment details and wish to confirm the payment.
     *
     * @param string $nextUrl URL to forward the customer to.
     * @param string $detail Optional human readable reasons for accepting the transaction.
     *
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function confirm()
    {
        return $this->getResponseBody();
    }
}
