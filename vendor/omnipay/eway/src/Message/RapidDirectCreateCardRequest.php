<?php
/**
 * eWAY Rapid Direct Create Card Request
 */

namespace Omnipay\Eway\Message;

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

        $data['Payment'] = array();
        $data['Payment']['TotalAmount'] = 0;

        $data['Method'] = 'CreateTokenCustomer';

        return $data;
    }

    protected function getEndpoint()
    {
        return $this->getEndpointBase().'/DirectPayment.json';
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, json_encode($data))
            ->setAuth($this->getApiKey(), $this->getPassword())
            ->send();

        return $this->response = new RapidDirectCreateCardResponse($this, $httpResponse->json());
    }
}
