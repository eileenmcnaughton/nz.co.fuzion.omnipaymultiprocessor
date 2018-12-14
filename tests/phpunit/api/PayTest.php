<?php

use CRM_Omnipaymultiprocessor_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use GuzzleHttp\Psr7\Response;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class api_PayTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  use \Civi\Test\Api3TestTrait;
  use HttpClientTestTrait;

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  /**
   * Test the pre-approval function.
   */
  public function testPayRest() {

    $contact = $this->callAPISuccess('Contact', 'create', ['first_name' => 'Xena', 'last_name' => 'Warrior Princess', 'contact_type' => 'Individual']);
    $this->addMockTokenResponse();
    $responseArr1 = [
      'id' => 'B-2L159413TW638025H',
      'state' => 'ACTIVE',
      'description' => 'regular payment',
      'payer' => ['payer_info' => ['email' => 'xena@example.com', 'first_name' => 'xena', 'last_name' => 'xena', 'payer_id' => 'xyz']],
      'plan' => ['type' => 'MERCHANT_INITIATED_BILLING'],
      'merchant_preferences' => ['accepted_pymt_type' => 'INSTANT'],
      'merchant' => ['payee_info' => ['email' => 'zeus@example.com']],
    ];

    $this->getMockClient()->addResponse(new Response(200, [], json_encode($responseArr1)));
    $this->getMockClient()->addResponse(new Response(201, [], $this->getRequest2()));


    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->getHttpClient()];

    $processor = $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_PayPal_Rest',
      'user_name' => 'abc',
      'password' => 'def',
      'is_test' => 1,
    ]);
    $result = $this->callAPISuccess('PaymentProcessor', 'pay', [
      'payment_processor_id' => $processor['id'],
      'check_permissions' => TRUE,
      'amount' => 10,
      'qfKey' => 'blah',
      'currency' => 'USD',
      'component' => 'contribute',
      'version' => 3,
      'email' => 'blah@example.org',
      'is_recur' => 1,
      'payment_token' => 'BA-193582913B4363822',
      'token' => 'BA-193582913B4363822',
      'contactID' => $contact['id'],
      'contributionRecurID' => 4,
    ])['values'][0];
    $this->assertEquals('B-2L159413TW638025H', $result['token']);

    $outbound = $this->getRequestBodies();

    $this->assertEquals('grant_type=client_credentials', $outbound[0]);
    $response2 = json_decode($outbound[1], TRUE);
    $this->assertEquals('BA-193582913B4363822', $response2['token_id']);

    $response3 = json_decode($outbound[2], TRUE);
    $this->assertEquals('sale', $response3['intent']);
    $this->assertEquals('paypal', $response3['payer']['payment_method']);
    $this->assertEquals('B-2L159413TW638025H', $response3['payer']['funding_instruments'][0]['billing']['billing_agreement_id']);
    $this->assertEquals([[
      'description' => $contact['id'],
      'amount' => ['total' => '10.00', 'currency' => 'USD'],
      'invoice_number' => '',
    ]], $response3['transactions']);

  }

  protected function addMockTokenResponse() {
    $this->getMockClient()->addResponse(new Response(200, [],
      '{"scope":"https://api.paypal.com/v1/payments/.* https://uri.paypal.com/services/payments/refund https://uri.paypal.com/services/applications/webhooks https://uri.paypal.com/services/payments/payment/authcapture https://uri.paypal.com/payments/payouts https://api.paypal.com/v1/vault/credit-card/.* https://uri.paypal.com/services/disputes/read-seller https://uri.paypal.com/services/subscriptions https://uri.paypal.com/services/disputes/read-buyer https://api.paypal.com/v1/vault/credit-card openid https://uri.paypal.com/services/disputes/update-seller https://uri.paypal.com/services/payments/realtimepayment",
"nonce":"2018-12-09T20:47:44ZQaSre3JCNsC4A1P6LyqcFe6PpK_MYEbOb6XuksJQibg",
"access_token":"A21AAF9dQkpsPNdkg99j1d_DIls9Zz_afB60FJrSUJm0zELjghCcnOdzpLeP_Ywk0f0LgPIfBfOa-vqCiaxLu_fh0TJBV_-3g",
"token_type":"Bearer",
"app_id":"APP-80W284485P519543T",
"expires_in":32400}'
    ));
  }

  protected function getRequest2() {
    return '{
 "id":"PAYID-LQIOOUI4YU493720B627830F",
"intent":"sale",
"state":"approved",
"payer":{
 "payment_method":"paypal",
"status":"VERIFIED",
"payer_info":{
 "email":"xena@example.com",
"first_name":"Xena",
"last_name":"Warrior",
"payer_id":"ARDXQC3Z9AR7E",
"country_code":"US",
"business_name":"Test Store"
},
"funding_instruments":[{
 "billing":{
 "billing_agreement_id":"B-2L159413TW638025H"
}
}]
},
"transactions":[{
 "amount":{
 "total":"10.00",
"currency":"USD",
"details":{
 "subtotal":"10.00",
"shipping":"0.00",
"insurance":"0.00",
"handling_fee":"0.00",
"shipping_discount":"0.00"
}
},
"payee":{
 "merchant_id":"36EFBE3DEDGDC",
"email":"paypalmerchant@example.com"
},
"description":"121 : 203-121-Help Support Civ",
"invoice_number":"121",
"item_list":{
 
},
"related_resources":[{
 "sale":{
 "id":"2EY25460EC203040T",
"state":"completed",
"amount":{
 "total":"10.00",
"currency":"USD",
"details":{
 "subtotal":"10.00",
"shipping":"0.00",
"insurance":"0.00",
"handling_fee":"0.00",
"shipping_discount":"0.00"
}
},
"payment_mode":"INSTANT_TRANSFER",
"protection_eligibility":"ELIGIBLE",
"protection_eligibility_type":"ITEM_NOT_RECEIVED_ELIGIBLE,UNAUTHORIZED_PAYMENT_ELIGIBLE",
"transaction_fee":{
 "value":"0.59",
"currency":"USD"
},
"billing_agreement_id":"B-2L159413TW638025H",
"parent_payment":"PAYID-LQIOOUI4YU493720B627830F",
"create_time":"2018-12-12T10:47:47Z",
"update_time":"2018-12-12T10:47:47Z",
"links":[{
 "href":"https://api.sandbox.paypal.com/v1/payments/sale/2EY25460EC203040T",
"rel":"self",
"method":"GET"
},
{
 "href":"https://api.sandbox.paypal.com/v1/payments/sale/2EY25460EC203040T/refund",
"rel":"refund",
"method":"POST"
},
{
 "href":"https://api.sandbox.paypal.com/v1/payments/payment/PAYID-LQIOOUI4YU493720B627830F",
"rel":"parent_payment",
"method":"GET"
}]
}
}]
}],"create_time":"2018-12-12T10:47:45Z",
"links":[{
 "href":"https://api.sandbox.paypal.com/v1/payments/payment/PAYID-LQIOOUI4YU493720B627830F",
"rel":"self",
"method":"GET"
}]
}';
  }

}
