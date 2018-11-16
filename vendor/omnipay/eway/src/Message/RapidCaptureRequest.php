<?php
/**
 * eWAY Rapid Capture Request
 */

namespace Omnipay\Eway\Message;

/**
 * eWAY Rapid Capture Request
 *
 * This is a request to capture and process a previously created authorisation.
 *
 * Example - note this example assumes that the authorisation has been successful
 *  and that the Transaction ID returned from the authorisation is held in $txn_id.
 *  See RapidDirectAuthorizeRequest for the first part of this example.
 *
 * <code>
 *   // Once the transaction has been authorized, we can capture it for final payment.
 *   $transaction = $gateway->capture(array(
 *       'amount'        => '10.00',
 *       'currency'      => 'AUD',
 *   ));
 *   $transaction->setTransactionReference($txn_id);
 *   $response = $transaction->send();
 * </code>
 *
 * @link https://eway.io/api-v3/#pre-auth
 * @see RapidDirectAuthorizeRequest
 */
class RapidCaptureRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount', 'transactionReference');

        $data = [];

        $data['Payment'] = [];
        $data['Payment']['TotalAmount'] = $this->getAmountInteger();
        $data['Payment']['InvoiceNumber'] = $this->getTransactionId();
        $data['Payment']['InvoiceDescription'] = $this->getDescription();
        $data['Payment']['CurrencyCode'] = $this->getCurrency();
        $data['Payment']['InvoiceReference'] = $this->getInvoiceReference();

        $data['TransactionId'] = $this->getTransactionReference();

        return $data;
    }

    public function getEndpoint()
    {
        return $this->getEndpointBase() . '/CapturePayment';
    }

    public function sendData($data)
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($this->getApiKey() . ':' . $this->getPassword()),
            'content-type' => 'application/json',
        ];

        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), $headers, json_encode($data));

        return $this->response = new RapidResponse($this, json_decode((string) $httpResponse->getBody(), true));
    }
}
