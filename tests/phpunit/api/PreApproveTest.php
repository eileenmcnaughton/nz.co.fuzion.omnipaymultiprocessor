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
class api_PreApproveTest extends \PHPUnit\Framework\TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  use \Civi\Test\Api3TestTrait;
  use HttpClientTestTrait;
  use PaypalRestTestTrait;

  /**
   * @return \Civi\Test\CiviEnvBuilder
   * @throws \CRM_Extension_Exception_ParseException
   */
  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * Test the pre-approval function.
   */
  public function testPreApproveRest() {
    $this->addMockTokenResponse();

    $this->getMockClient()->addResponse(new Response(200, [], "{\"id\":\"PAY-79M30569TN7125128LQGYFLI\",\"intent\":\"sale\",\"state\":\"created\",\"payer\":{\"payment_method\":\"paypal\"},\"transactions\":[{\"amount\":{\"total\":\"10.00\",\"currency\":\"USD\"},\"description\":\"false\",\"invoice_number\":\"\",\"related_resources\":[]}],\"create_time\":\"2018-12-09T21:01:32Z\",\"links\":[{\"href\":\"https:\/\/api.sandbox.paypal.com\/v1\/payments\/payment\/PAY-79M30569TN7125128LQGYFLI\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\/\/www.sandbox.paypal.com\/cgi-bin\/webscr?cmd=_express-checkout&token=EC-9T988732661526452\",\"rel\":\"approval_url\",\"method\":\"REDIRECT\"},{\"href\":\"https:\/\/api.sandbox.paypal.com\/v1\/payments\/payment\/PAY-79M30569TN7125128LQGYFLI\/execute\",\"rel\":\"execute\",\"method\":\"POST\"}]}"));


    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->getHttpClient()];

    $processor = $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_PayPal_Rest',
      'user_name' => 'abc',
      'name' => 'PayPal_Rest',
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
      'validate' => []
    ])['values'][0];
    $this->assertEquals('EC-9T988732661526452', $preApproval['token']);

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

  /**
   * Test the pre-approval function for recurring transactions.
   */
  public function testPreApproveRestRecur() {
    $this->addMockTokenResponse();
    $response = [
      'links' => [
        [
          'href' => 'https://www.sandbox.paypal.com/agreements/approve?ba_token=BA-246293876N953433E',
          'rel' => 'approval_url',
          'method' => 'POST',
        ], [
          'href' => 'https://api.sandbox.paypal.com/v1/billing-agreements/BA-246293876N953433E/agreements',
          'rel' => 'self',
          'method' => 'POST',
        ],
      ],
      'token_id' => 'BA-246293876N953433E',
    ];
    $this->getMockClient()->addResponse(new Response(200, [], json_encode($response)));

    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->getHttpClient()];
    $processor = $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_PayPal_Rest',
      'name' => 'PayPal_Rest',
      'user_name' => 'AWzymvrczbgFT9CuhILzNXnXFyLXsxa8lacr_TJbOT4ytdRuaKnr73t1kOIdwbSTmnjTuajgKaiZCjqR',
      'password' => 'EANpVE9liVxABP173oGLic1fhoK2gixGeVCrXjR4Q_dpO2FLMMTtyYSmhhe5IZDaQaPUsmc4Jkx7CQGy',
      'is_test' => 1,
    ]);
    $preApproval = civicrm_api('PaymentProcessor', 'preapprove', [
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
    ])['values'][0];
    $this->assertEquals('BA-246293876N953433E', $preApproval['token']);


    $outbound = $this->getRequestBodies();
    $mainRequest = json_decode($outbound[1], TRUE);
    $this->assertEquals('Recurring payment', $mainRequest['description']);
    $this->assertEquals(['payment_method' => 'PAYPAL'], $mainRequest['payer']);
    $this->assertEquals('MERCHANT_INITIATED_BILLING', $mainRequest['plan']['type']);
    $this->assertEquals('INSTANT', $mainRequest['plan']['merchant_preferences']['accepted_pymt_type']);
    $this->assertEquals(FALSE, $mainRequest['plan']['merchant_preferences']['immutable_shipping_address']);
    $this->assertEquals(TRUE, $mainRequest['plan']['merchant_preferences']['skip_shipping_address']);

    $this->assertEquals([
      0 => 'https://api.sandbox.paypal.com/v1/oauth2/token',
      1 => 'https://api.sandbox.paypal.com/v1/billing-agreements/agreement-tokens',
    ], $this->getRequestURLs());
    }

}
