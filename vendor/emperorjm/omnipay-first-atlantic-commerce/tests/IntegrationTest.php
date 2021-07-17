<?php

namespace tests;


use Omnipay\FirstAtlanticCommerce\Gateway;
use Omnipay\FirstAtlanticCommerce\Message\AuthorizeResponse;
use Omnipay\FirstAtlanticCommerce\Message\CreateCardResponse;
use Omnipay\FirstAtlanticCommerce\Message\TransactionModificationResponse;
use Omnipay\FirstAtlanticCommerce\Message\UpdateCardResponse;
use Omnipay\Tests\GatewayTestCase;

/**
 * Class IntegrationTest
 *
 * Integration tests will communicate with the First Atlantic commerce sandbox so you will need credentials for that
 * environment. You will then need to set those credentials in the myCredentials.json file in the tests folder. The
 * format of that json file is as follows:
 *
 * {
 *     "merchantId":"<your ID>",
 *     "merchantPassword":"<you password>"
 * }
 *
 * If myCredentials.json does not exists or the json is not complete, all tests in this class will be skipped.
 *
 * @package tests
 */
class IntegrationTest extends GatewayTestCase
{
    /** @var  Gateway */
    protected $gateway;

    /**
     * Checks to make sure that myCredentials.json exists and has the correct credentials configured. If they are, it sets
     * up the gateway instance. If they are not, it will skip the tests in this class.
     */
    public function setUp()
    {
        $merchantId = '';
        $merchantPassword = '';
        $credentialsFilePath = dirname(__FILE__) . '/myCredentials.json';

        if(file_exists($credentialsFilePath)) {
            $credentialsJson = file_get_contents($credentialsFilePath);
            if($credentialsJson) {
                $credentials = json_decode($credentialsJson);
                $merchantId = $credentials->merchantId;
                $merchantPassword = $credentials->merchantPassword;
            }
        }

        if(empty($merchantId) || empty($merchantPassword)) {
            $this->markTestSkipped();
        } else {
            $this->gateway = new Gateway();
            $this->gateway->setMerchantId($merchantId);
            $this->gateway->setMerchantPassword($merchantPassword);
            $this->gateway->setTestMode(true);
            $this->gateway->setRequireAvsCheck(false);
        }
    }

    /**
     * Runs through an authorize, capture, and refund request to test that they are coming back from FAC as expected.
     */
    public function testAuthorizeCapture()
    {
        $transactionId = uniqid();
        /** @var AuthorizeResponse $authResponse */
        $authResponse = $this->gateway->authorize([
            'amount'        => '15.00',
            'currency'      => 'USD',
            'transactionId' => $transactionId,
            'card'          => $this->getValidCard()
        ])->send();

        $this->assertTrue($authResponse->isSuccessful(), 'Authorize should succeed');
        $this->assertEquals($transactionId, $authResponse->getTransactionId());

        /** @var TransactionModificationResponse $captureResponse */
        $captureResponse = $this->gateway->capture([
            'amount'        => '15.00',
            'currency'      => 'USD',
            'transactionId' => $transactionId
        ])->send();

        $this->assertTrue($captureResponse->isSuccessful(), 'Capture should succeed');
        $this->assertEquals($transactionId, $captureResponse->getTransactionId());

        /** @var TransactionModificationResponse $refundResponse */
        $refundResponse = $this->gateway->refund([
            'amount'        => '15.00',
            'currency'      => 'USD',
            'transactionId' => $transactionId
        ])->send();

        $this->assertTrue($refundResponse->isSuccessful(), 'Refund should succeed');
        $this->assertEquals($transactionId, $refundResponse->getTransactionId());
    }

    /**
     * Runs through a purchase, void, refund request to make sure that they are coming back from FAC as expected. FAC
     * seems to be auto settling captured transactions in their sandbox so the void request is going to come back as false.
     */
    public function testPurchaseVoidRefund()
    {
        $transactionId = uniqid();
        /** @var AuthorizeResponse $purchaseResponse */
        $purchaseResponse = $this->gateway->purchase([
            'amount' => '20.00',
            'currency' => 'USD',
            'transactionId' => $transactionId,
            'card' => $this->getValidCard()
        ])->send();

        $this->assertTrue($purchaseResponse->isSuccessful(), 'Purchase should succeed');
        $this->assertEquals($transactionId, $purchaseResponse->getTransactionId());

        /** @var TransactionModificationResponse $voidResponse */
        $voidResponse = $this->gateway->void([
            'amount' => '20.00',
            'currency' => 'USD',
            'transactionId' => $transactionId
        ])->send();

        $this->assertFalse($voidResponse->isSuccessful(), 'Void should fail');
        $this->assertEquals($transactionId, $voidResponse->getTransactionId());

        /** @var TransactionModificationResponse $refundResponse */
        $refundResponse = $this->gateway->refund([
            'amount' => '20.00',
            'currency' => 'USD',
            'transactionId' => $transactionId
        ])->send();

        $this->assertTrue($refundResponse->isSuccessful(), 'Purchase refund should succeed');
        $this->assertEquals($transactionId, $refundResponse->getTransactionId());
    }

    /**
     * Test the creation and update of a card through FAC
     */
    public function testCreateUpdateCard()
    {
        /** @var CreateCardResponse $createResponse */
        $createResponse = $this->gateway->createCard([
            'customerReference' => 'John Doe',
            'card' => $this->getValidCard()
        ])->send();

        $this->assertTrue($createResponse->isSuccessful(), 'Card Creation should have worked');
        $this->assertNotEmpty($createResponse->getCardReference());

        /** @var UpdateCardResponse $updateResponse */
        $updateResponse = $this->gateway->updateCard([
            'customerReference' => 'Jane Doe',
            'cardReference' => $createResponse->getCardReference(),
            'card' => $this->getValidCard()
        ])->send();

        $this->assertTrue($updateResponse->isSuccessful());
        $this->assertNotEmpty($updateResponse->getCardReference());
    }

    /**
     * Test the status request through FAC
     */
    public function testStatus()
    {
        $transactionId = uniqid();
        $authorizeResponse = $this->gateway->authorize([
            'amount' => '30.00',
            'currency' => 'USD',
            'transactionId' => $transactionId,
            'card' => $this->getValidCard()
        ])->send();

        $this->assertTrue($authorizeResponse->isSuccessful());

        $statusResponse = $this->gateway->status([
            'transactionId' => $transactionId
        ])->send();

        $this->assertTrue($statusResponse->isSuccessful());
        $this->assertEquals('Transaction is approved.', $statusResponse->getMessage());
    }

    /**
     * Test an Authorize message that also create's a card in FAC's system from the data in the authorize message.
     */
    public function testAuthorizeWithCreateCard()
    {
        $transactionId = uniqid();
        /** @var AuthorizeResponse $authorizeResponse */
        $authorizeResponse = $this->gateway->authorize([
            'amount' => '30.00',
            'currency' => 'USD',
            'transactionId' => $transactionId,
            'card' => $this->getValidCard(),
            'createCard' => true
        ])->send();

        $this->assertTrue($authorizeResponse->isSuccessful());
        $this->assertNotEmpty($authorizeResponse->getCardReference());
    }
}