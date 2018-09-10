<?php

use CRM_Omnipaymultiprocessor_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Client as HttpClient;

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
  use Guzzle3TestTrait;

  /**
   * @var Guzzle\Http\Client
   */
  protected $httpClient;

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

    $this->setMockHttpResponse([
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

    $this->setUpClientWithHistoryContainer();
    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->guzzleClient];
    $processor = $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_PayPal_Rest',
      'user_name' => 'AWzymvrczbgFT9CuhILzNXnXFyLXsxa8lacr_TJbOT4ytdRuaKnr73t1kOIdwbSTmnjTuajgKaiZCjqR',
      'password' => 'EANpVE9liVxABP173oGLic1fhoK2gixGeVCrXjR4Q_dpO2FLMMTtyYSmhhe5IZDaQaPUsmc4Jkx7CQGy',
      'is_test' => 1,
    ]);
    $preApproval = civicrm_api('PaymentProcessor', 'preapprove', [
      'payment_processor_id' => $processor['id'],
      'check_permissions' => TRUE,
      'amount' => 10,
      'qfKey' => 'blah',
      'currency' => 'USD',
      'is_recur' => TRUE,
      'component' => 'contribute',
      'installments' => 3,
      'frequency_unit' => 'week',
      'frequency_interval' => 3,
      'version' => 3,
      'is_test' => 1,
      'email' => 'blah@example.org',
    ]);
    $outbound = $this->getRequestBodies();
    $inbound = $this->getResponseBodies();

    $this->assertEquals('EC-654429990B3545832', $preApproval['values'][0]['token']);
  }

  public function testFinishTokenPayment() {
    $this->setUpClientWithHistoryContainer();
    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->guzzleClient];
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
    $inbound = $this->getResponseBodies();
$this->assertResponsesOk();
    $this->assertEquals('EC-654429990B3545832', $preApproval['values'][0]['token']);

  //
    }
  /**
   * @return \HttpClient
   */
  public function getHttpClient() {
    if (null === $this->httpClient) {
      $this->httpClient = new HttpClient;
    }
    return $this->httpClient;
  }

  /**
   * Set a mock response from a mock file on the next client request.
   *
   * This method assumes that mock response files are located under the
   * Mock/ subdirectory of the current class. A mock response is added to the next
   * request sent by the client.
   *
   * An array of path can be provided and the next x number of client requests are
   * mocked in the order of the array where x = the array length.
   *
   * @param array|string $paths Path to files within the Mock folder of the service
   *
   * @return MockPlugin returns the created mock plugin
   */
  public function setMockHttpResponse($body)
  {
    $plugin = new \Guzzle\Plugin\Mock\MockPlugin();
    $response = new \Guzzle\Http\Message\Response(200);
    $response->setBody($body);
    $plugin->addResponse($response);
    $this->getHttpClient()->getEventDispatcher()->addSubscriber($plugin);
  }

}
