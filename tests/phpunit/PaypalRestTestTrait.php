<?php

use GuzzleHttp\Psr7\Response;

/**
 * Class PaypalRestTestTrait
 *
 * This trait defines a number of helper functions for testing Paypal Rest.
 */
trait PaypalRestTestTrait {

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
}
