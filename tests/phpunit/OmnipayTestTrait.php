<?php

/**
 * Class OmnipayTestTrait
 *
 * This trait defines a number of helper functions for testing Paypal Rest.
 */
trait OmnipayTestTrait {

  /**
   * @param string $type
   *
   * @return array
   */
  protected function createTestProcessor($type) {
    return $this->callAPISuccess('PaymentProcessor', 'create', [
      'payment_processor_type_id' => 'omnipay_' . $type,
      'user_name' => 'abd',
      'password' => 'def',
      'is_test' => 1,
    ]);
  }

}
