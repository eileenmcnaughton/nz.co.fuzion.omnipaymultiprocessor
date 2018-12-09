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
class api_PreApproveTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
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
   * Test the preapproval function.
   */
  public function testPreApproveExpress() {

    $this->setMockHttpResponseToArray([
      'TOKEN' => 'EC-654429990B3545832',
      'TIMESTAMP' => '2018-07-30T07:11:48Z',
      'CORRELATIONID' => '2893c8052cf1c',
      'ACK' => 'Success',
      'VERSION' => '119.0',
      'BUILD' => '47733884',
    ]);
    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->getHttpClient()];
    $processor = $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_PayPal_Express',
    ]);
    $preApproval = $this->callAPISuccess('PaymentProcessor', 'preapprove', [
      'payment_processor_id' => $processor['id'],
      'check_permissions' => TRUE,
      'amount' => 10,
      'qfKey' => 'blah',
      'currency' => 'USD',
    ]);
    $this->assertEquals('EC-654429990B3545832', $preApproval['values'][0]['token']);
  }

  /**
   * Test the preapproval function.
   */
  public function testPreApproveRest() {
    $this->getMockClient()->addResponse(new Response(200, [],
      '{"scope":"https://api.paypal.com/v1/payments/.* https://uri.paypal.com/services/payments/refund https://uri.paypal.com/services/applications/webhooks https://uri.paypal.com/services/payments/payment/authcapture https://uri.paypal.com/payments/payouts https://api.paypal.com/v1/vault/credit-card/.* https://uri.paypal.com/services/disputes/read-seller https://uri.paypal.com/services/subscriptions https://uri.paypal.com/services/disputes/read-buyer https://api.paypal.com/v1/vault/credit-card openid https://uri.paypal.com/services/disputes/update-seller https://uri.paypal.com/services/payments/realtimepayment",
"nonce":"2018-12-09T20:47:44ZQaSre3JCNsC4A1P6LyqcFe6PpK_MYEbOb6XuksJQibg",
"access_token":"A21AAF9dQkpsPNdkg99j1d_DIls9Zz_afB60FJrSUJm0zELjghCcnOdzpLeP_Ywk0f0LgPIfBfOa-vqCiaxLu_fh0TJBV_-3g",
"token_type":"Bearer",
"app_id":"APP-80W284485P519543T",
"expires_in":32400}'
    ));

    $this->getMockClient()->addResponse(new Response(200, [], "{\"id\":\"PAY-79M30569TN7125128LQGYFLI\",\"intent\":\"sale\",\"state\":\"created\",\"payer\":{\"payment_method\":\"paypal\"},\"transactions\":[{\"amount\":{\"total\":\"10.00\",\"currency\":\"USD\"},\"description\":\"false\",\"invoice_number\":\"\",\"related_resources\":[]}],\"create_time\":\"2018-12-09T21:01:32Z\",\"links\":[{\"href\":\"https:\/\/api.sandbox.paypal.com\/v1\/payments\/payment\/PAY-79M30569TN7125128LQGYFLI\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\/\/www.sandbox.paypal.com\/cgi-bin\/webscr?cmd=_express-checkout&token=EC-9T988732661526452\",\"rel\":\"approval_url\",\"method\":\"REDIRECT\"},{\"href\":\"https:\/\/api.sandbox.paypal.com\/v1\/payments\/payment\/PAY-79M30569TN7125128LQGYFLI\/execute\",\"rel\":\"execute\",\"method\":\"POST\"}]}"));


    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->getHttpClient()];

    $processor = $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_PayPal_Rest',
      'user_name' => 'abc',
      'password' => 'def',
      'is_test' => 1,
    ]);
    $preApproval = $this->callAPISuccess('PaymentProcessor', 'preapprove', [
      'payment_processor_id' => $processor['id'],
      'check_permissions' => TRUE,
      'amount' => 10,
      'qfKey' => 'blah',
      'currency' => 'USD',
      'component' => 'contribute',
      'version' => 3,
      'email' => 'blah@example.org',
    ])['values'][0];
    $this->assertEquals('PAY-79M30569TN7125128LQGYFLI', $preApproval['token']);

    $outbound = $this->getRequestBodies();

    $this->assertEquals('grant_type=client_credentials', $outbound[0]);
    $response2 = json_decode($outbound[1], TRUE);
    $this->assertEquals('sale', $response2['intent']);
    $this->assertEquals('paypal', $response2['payer']['payment_method']);
    $this->assertEquals([[
      'description' => false,
      'amount' => ['total' => '10.00', 'currency' => 'USD'],
      'invoice_number' => '',
        ],
    ], $response2['transactions']);
  }

  public function testFinishTokenPayment() {
    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->getHttpClient()];
    $processor = $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_PayPal_Rest',
      'user_name' => 'AWzymvrczbgFT9CuhILzNXnXFyLXsxa8lacr_TJbOT4ytdRuaKnr73t1kOIdwbSTmnjTuajgKaiZCjqR',
      'password' => 'EANpVE9liVxABP173oGLic1fhoK2gixGeVCrXjR4Q_dpO2FLMMTtyYSmhhe5IZDaQaPUsmc4Jkx7CQGy',
      'is_test' => 1,
    ]);
    $preApproval = civicrm_api('PaymentProcessor', 'pay', [
      'payment_processor_id' => $processor['id'],
      'check_permissions' => TRUE,
      'amount' => 60,
      'qfKey' => 'blah',
      'currency' => 'USD',
      'is_recur' => TRUE,
      'component' => 'contribute',
      'installments' => 3,
      'frequency_unit' => 'week',
      'frequency_interval' => 3,
      'version' => 3,
      'is_test' => 1,
      'token' => 'BA-9RH77653MG862110M',
    ]);
    $outbound = $this->getRequestBodies();
    //$this->assertEquals('EC-654429990B3545832', $preApproval['values'][0]['token']);

  //
    }

}
