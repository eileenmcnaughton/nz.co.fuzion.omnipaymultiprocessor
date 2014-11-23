<?php
namespace Omnipay\Skrill\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Skrill Request
 *
 * The Skrill Automated Payments Interface enables you to execute automated requests to
 * Skrill, including:
 *
 * * Send money transactions to customers
 * * Skrill 1-Tap transactions
 * * Manage recurring payments
 * * Checking the status of transactions and recurring payments
 * * Downloading account histories and repost status reports
 *
 * @author Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license http://opensource.org/licenses/mit-license.php MIT
 * @version 2.16 Automated Payments Interface
 */
abstract class Request extends AbstractRequest
{
    /**
     * Get the endpoint for this request.
     *
     * @return string endpoint
     */
    abstract protected function getEndpoint();

    /**
     * Get the action name for this request.
     *
     * @return string action name
     */
    abstract protected function getAction();

    /**
     * Get the data for this request.
     *
     * @return array request data
     */
    public function getData()
    {
        $data['action'] = $this->getAction();

        return $data;
    }

    /**
     * Send the skrill request.
     *
     * @return Response
     */
    public function sendData($data)
    {
        $url = $this->getEndpoint() . '?' . http_build_query($data);
        $httpResponse = $this->httpClient->get($url)->send();

        $xml = $httpResponse->xml();
        return $this->createResponse($xml);
    }

    /**
     * Create a proper response based on the request.
     *
     * @param  \SimpleXMLElement  $xml  raw response
     * @return Response                 response for this request
     */
    protected function createResponse($xml)
    {
        $requestClass = get_class($this);
        $responseClass = substr($requestClass, 0, -7) . 'Response';
        return $this->response = new $responseClass($this, $xml);
    }
}
