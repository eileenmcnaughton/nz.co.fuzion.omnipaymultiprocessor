<?php

namespace Omnipay\SystemPay\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    public function testConstruct()
    {
        $response = new CompletePurchaseResponse( $this->getMockRequest(), array(
            'vads_amount' => '3000',
            'vads_auth_mode' => 'FULL',
            'vads_auth_number' => '3fb0de',
            'vads_auth_result' => '00',
            'vads_capture_delay' => '0',
            'vads_card_brand' => 'VISA',
            'vads_card_number' => '497010XXXXXX0000',
            'vads_payment_certificate' => 'a50d15063b5ec6cb140043138b8d7576470b71a9',
            'vads_ctx_mode' => 'TEST',
            'vads_currency' => '978',
            'vads_effective_amount' => '3000',
            'vads_site_id' => '12345678',
            'vads_trans_date' => '20140902094139',
            'vads_trans_id' => '454058',
            'vads_validation_mode' => '0',
            'vads_version' => 'V2',
            'vads_warranty_result' => 'YES',
            'vads_payment_src' => 'EC',
            'vads_sequence_number' => '1',
            'vads_contract_used' => '5785350',
            'vads_trans_status' => 'AUTHORISED',
            'vads_expiry_month' => '6',
            'vads_expiry_year' => '2015',
            'vads_bank_code' => '17807',
            'vads_bank_product' => 'A',
            'vads_pays_ip' => 'FR',
            'vads_presentation_date' => '20140902094202',
            'vads_effective_creation_date' => '20140902094202',
            'vads_operation_type' => 'DEBIT',
            'vads_threeds_enrolled' => 'Y',
            'vads_threeds_cavv' => 'Q2F2dkNhdnZDYXZ2Q2F2dkNhdnY=',
            'vads_threeds_eci' => '05',
            'vads_threeds_xid' => 'WXJsVXpHVjFoMktzNmw5dTd1ekQ=',
            'vads_threeds_cavvAlgorithm' => '2',
            'vads_threeds_status' => 'Y',
            'vads_threeds_sign_valid' => '1',
            'vads_threeds_error_code' => '',
            'vads_threeds_exit_status' => '10',
            'vads_risk_control' => 'CARD_FRAUD=OK;COMMERCIAL_CARD=OK',
            'vads_result' => '00',
            'vads_extra_result' => '00',
            'vads_card_country' => 'FR',
            'vads_language' => 'fr',
            'vads_hash' => '299d81f4b175bfb7583d904cd19ef5e38b2b79b2373d9b2b4aab74e5753b10bc',
            'vads_url_check_src' => 'PAY',
            'vads_action_mode' => 'INTERACTIVE',
            'vads_payment_config' => 'SINGLE',
            'vads_page_action' => 'PAYMENT',
            'signature' => '3132f1e451075f2408cda41f2e647e9b4747d421',
        ) );


        $this->assertTrue( $response->isSuccessful() );
        $this->assertFalse( $response->isRedirect() );
        $this->assertSame( '00', $response->getCode() );

        $this->assertSame( '454058', $response->getTransactionReference() );
        $this->assertSame( '20140902094139', $response->getTransactionDate() );
    }
}
