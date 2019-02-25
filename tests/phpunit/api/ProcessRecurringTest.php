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
class api_ProcessRecurringTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  use \Civi\Test\Api3TestTrait;
  use HttpClientTestTrait;
  use PaypalRestTestTrait;

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * Test the process recurring job.
   */
  public function testProcessRecurring() {
    CRM_Core_Config::singleton()->userSystem->setMySQLTimeZone();
    $this->addMockTokenResponse();
    $response = $this->getResponseBody();
    $this->getMockClient()->addResponse(new Response(201, [], json_encode($response)));
    Civi::$statics['Omnipay_Test_Config'] = ['client' => $this->getHttpClient()];

    $contact = $this->callAPISuccess('Contact', 'create', ['first_name' => 'Xena', 'last_name' => 'Warrior Princess', 'contact_type' => 'Individual']);

    $processor = $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_PayPal_Rest',
      'user_name' => 'ABC',
      'password' => 'DEF',
      'is_test' => 1,
      'is_active' => 1,
    ]);
    $paymentToken = $this->callAPISuccess('PaymentToken', 'create', [
      'payment_processor_id' => $processor['id'],
      'token' => 'B-ABCDEFGHTW638025H',
      'contact_id' => $contact['id'],
    ]);

    $contributionRecur = $this->callAPISuccess('ContributionRecur', 'create', [
      'payment_processor_id' => $processor['id'],
      'next_sched_contribution_date' => 'today',
      'contact_id' => $contact['id'],
      'frequency_interval' => 1,
      'frequency_unit' => 'month',
      'amount' => 10,
      'contribution_status_id' => 'Pending',
      'is_test' => TRUE,
      'payment_token_id' => $paymentToken['id'],
    ]);

    $this->callAPISuccess('Contribution', 'create', [
      'total_amount' => 10,
      'contact_id' => $contact['id'],
      'financial_type_id' => 'Donation',
      'contribution_status_id' => 'Completed',
      'is_test' => TRUE,
      'contribution_recur_id' => $contributionRecur['id'],
      'payment_processor_id' => $processor['id'],
      'receive_date' => '1 month ago',
    ]);
    $this->callAPISuccess('ContributionRecur', 'create', ['id' => $contributionRecur['id'], 'next_sched_contribution_date' => 'today']);

    $result = $this->callAPISuccess('Job', 'process_recurring', [])['values'];
    $this->assertEquals(1, count($result['success']), print_r($result, 1));

    $outbound = $this->getRequestBodies();
    $paymentRequest = json_decode($outbound[1], TRUE);
    $this->assertEquals('grant_type=client_credentials', $outbound[0]);
    $this->assertEquals('sale', $paymentRequest['intent']);
    $this->assertEquals('paypal', $paymentRequest['payer']['payment_method']);
    $this->assertEquals(['billing' => ['billing_agreement_id' => 'B-ABCDEFGHTW638025H']], $paymentRequest['payer']['funding_instruments'][0]);
    $this->assertEquals(['total' => '10.00', 'currency' => 'USD'], $paymentRequest['transactions'][0]['amount']);

    $contributionRecur = $this->callAPISuccessGetSingle('ContributionRecur', ['id' => $contributionRecur['id']]);
    //$this->assertEquals(strtotime('+ 1 month'), strtotime($contributionRecur['next_sched_contribution_date']));
    $this->assertEquals(0, $contributionRecur['failure_count']);
    $contributions = $this->callAPISuccess('Contribution', 'get', [
      'contribution_recur_id' => $contributionRecur['id'],
      'is_test' => TRUE,
      'options' => ['sort' => 'receive_date DESC']
    ]);
    $this->assertEquals(2, $contributions['count']);
    $this->assertEquals($paymentRequest['transactions'][0]['invoice_number'], reset($contributions['values'])['id'], print_r($paymentRequest, 1) . print_r($this->callAPISuccess('Contribution', 'get', ['is_test' => ''])));
  }

  public function getRequestBody() {

    // url is /v1/payments/payment
    return '{"intent":"sale","payer":{"payment_method":"paypal","funding_instruments":[{"billing":{"billing_agreement_id":"B-2L159413TW638025H"}}]},"transactions":[{"description":"50 : 40-50-Repeat payment, or","amount":{"total":"10.00","currency":"USD"},"invoice_number":"50"}],"experience_profile_id":null}';


  }

  public function getResponseBody() {
    $r = [
      'id' => 'PAYID-LQLNHWY5J6312161P160841J',
      'intent' => 'sale',
      'state' => 'approved',
      'payer' =>
        [
          'payment_method' => 'paypal',
          'status' => 'VERIFIED',
          'payer_info' =>
            [
              'email' => 'demo@example.com',
              'first_name' => 'Demo',
              'last_name' => 'Example',
              'payer_id' => 'ABCDEFGHI9AR7E',
              'country_code' => 'US',
              'business_name' => 'Test Store',
            ],
          'funding_instruments' =>
            [
              ['billing' => ['billing_agreement_id' => 'B-ABCDEFGHTW638025H']],
            ],
        ],
      'transactions' => [
        [
          'amount' =>
            [
              'total' => '10.00',
              'currency' => 'USD',
              'details' =>
                [
                  'subtotal' => '10.00',
                  'shipping' => '0.00',
                  'insurance' => '0.00',
                  'handling_fee' => '0.00',
                  'shipping_discount' => '0.00',
                ],
            ],
          'payee' =>
            [
              'merchant_id' => 'ABCDEFGH',
              'email' => 'paypalmerchant@example.com',
            ],
          'description' => '50 : 40-50-Repeat payment, or',
          'invoice_number' => '50',
          'item_list' => [],
        ],
        'related_resources' =>
          [
            [
              'sale' =>
                [
                  'id' => '80E35695UU540184X',
                  'state' => 'completed',
                  'amount' =>
                    [
                      'total' => '10.00',
                      'currency' => 'USD',
                      'details' =>
                        [
                          'subtotal' => '10.00',
                          'shipping' => '0.00',
                          'insurance' => '0.00',
                          'handling_fee' => '0.00',
                          'shipping_discount' => '0.00',
                        ],
                    ],
                ],
            ],
            'payment_mode' => 'INSTANT_TRANSFER',
            'protection_eligibility' => 'ELIGIBLE',
            'protection_eligibility_type' => 'ITEM_NOT_RECEIVED_ELIGIBLE,UNAUTHORIZED_PAYMENT_ELIGIBLE',
            'transaction_fee' =>
              [
                'value' => '0.59',
                'currency' => 'USD',
              ],
        ],
        'billing_agreement_id' => 'B-ABCDEFGHTW638025H',
        'parent_payment' => 'PAYID-LQLNHWY5J6312161P160841J',
        'create_time' => '2018-12-16T22:38:21Z',
        'update_time' => '2018-12-16T22:38:21Z',
        'links' => [
            [
              'href' => 'https://api.sandbox.paypal.com/v1/payments/sale/80E35695UU540184X',
              'rel' => 'self',
              'method' => 'GET',
            ],

            [
              'href' => 'https://api.sandbox.paypal.com/v1/payments/sale/80E35695UU540184X/refund',
              'rel' => 'refund',
              'method' => 'POST',
            ],

            [
              'href' => 'https://api.sandbox.paypal.com/v1/payments/payment/PAYID-LQLNHWY5J6312161P160841J',
              'rel' => 'parent_payment',
              'method' => 'GET',
            ],
          ],
      ],

      'create_time' => '2018-12-16T22:38:19Z',
      'links' =>
        [
          [
            'href' => 'https://api.sandbox.paypal.com/v1/payments/payment/PAYID-LQLNHWY5J6312161P160841J',
            'rel' => 'self',
            'method' => 'GET',
          ],
        ],
    ];
  }
}
