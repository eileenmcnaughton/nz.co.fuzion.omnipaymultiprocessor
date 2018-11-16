<?php
/**
 * eWAY Rapid Direct Create Card Request
 */

namespace Omnipay\Eway\Message;

use Omnipay\Omnipay;

/**
 * eWAY Rapid Direct Create Card Request
 *
 * Securely stores card details with eWAY as tokens.
 * Once submitted, a TokenCustomerID is provided which can be
 * used in future transactions instead of the card details.
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the eWAY Direct Gateway
 *   $gateway = Omnipay::create('Eway_RapidDirect');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *      'apiKey' => 'Rapid API Key',
 *      'password' => 'Rapid API Password',
 *      'testMode' => true, // Or false when you are ready for live transactions
 *   ));
 *
 *   // Create a credit card object
 *   $card = new CreditCard(array(
 *             'firstName'          => 'Example',
 *             'lastName'           => 'User',
 *             'number'             => '4444333322221111',
 *             'expiryMonth'        => '01',
 *             'expiryYear'         => '2020',
 *             'billingAddress1'    => '1 Scrubby Creek Road',
 *             'billingCountry'     => 'AU',
 *             'billingCity'        => 'Scrubby Creek',
 *             'billingPostcode'    => '4999',
 *             'billingState'       => 'QLD',
 *   ));
 *
 *   // Do a create card transaction on the gateway
 *   $request = $gateway->createCard(array(
 *      'card'              => $card,
 *   ));
 *
 *   $response = $request->send();
 *   if ($response->isSuccessful()) {
 *       $cardReference = $response->getCardReference();
 *   }
 * </code>
 *
 * @link https://eway.io/api-v3/#direct-connection
 * @link https://eway.io/api-v3/#token-payments
 */
class RapidDirectCreateCardRequest extends RapidDirectAbstractRequest
{
    public function getData()
    {
        $data = $this->getBaseData();

        $data['Payment'] = [];
        $data['Payment']['TotalAmount'] = 0;

        $data['Method'] = 'CreateTokenCustomer';

        return $data;
    }

    protected function getEndpoint()
    {
        return $this->getEndpointBase() . '/DirectPayment.json';
    }

    public function sendData($data)
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($this->getApiKey() . ':' . $this->getPassword())
        ];

        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), $headers, json_encode($data));

        $this->response = new RapidDirectCreateCardResponse(
            $this,
            json_decode((string) $httpResponse->getBody(), true)
        );

        if ($this->getAction() === 'Purchase' && $this->response->isSuccessful()) {
            $purchaseGateway = Omnipay::create('Eway_RapidDirect');
            $purchaseGateway->setApiKey($this->getApiKey());
            $purchaseGateway->setPassword($this->getPassword());
            $purchaseGateway->setTestMode($this->getTestMode());
            $purchaseResponse = $purchaseGateway->purchase([
                'amount' => $this->getAmount(),
                'currency' => $this->getCurrency(),
                'description' => $this->getDescription(),
                'transactionId' => $this->getTransactionId(),
                'card' => $this->getCard(),
                'cardReference' => $this->response->getCardReference(),
            ])->send();
            $this->response->setPurchaseResponse($purchaseResponse);
        }

        return $this->response;
    }
}
