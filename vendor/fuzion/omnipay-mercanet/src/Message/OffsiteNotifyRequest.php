<?php

namespace Omnipay\Mercanet\Message;

use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\NotificationInterface;
use Guzzle\Http\ClientInterface;

/**
 * Sage Pay Server Notification.
 * The gateway will send the results of Server transactions here.
 */
class OffsiteNotifyRequest extends AbstractRequest implements NotificationInterface
{
  /**
   * Copy of the POST data sent in.
   */
  protected $data;

    /**
     * Initialise the data from the server request.
     */
    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);

        // Grab the data from the request if we don't already have it.
        // This would be a good place to convert the encoding if required
        // e.g. ISO-8859-1 to UTF-8.

        $this->data = $httpRequest->request->all();
    }

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

  }

  /**
   * Response Textual Message
   *
   * @return string A response message from the payment gateway
   */
  public function getMessage()
  {
    return $this->getDataItem('StatusDetail');
  }

}
