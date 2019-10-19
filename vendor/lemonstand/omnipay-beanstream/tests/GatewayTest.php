<?php namespace Omnipay\Beanstream;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /** @var  Gateway */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(
            array(
                'amount' => '10.00'
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\AuthorizeRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('POST', $request->getHttpMethod());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(
            array(
                'amount' => '10.00'
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('POST', $request->getHttpMethod());
    }

    public function testCreateProfileWithCard()
    {
        $request = $this->gateway->createProfile(
            array(
                'language' => 'test-language',
                'comment' => 'test-comment'
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\CreateProfileRequest', $request);
        $this->assertSame('test-language', $request->getLanguage());
        $this->assertSame('test-comment', $request->getComment());
        $this->assertSame('POST', $request->getHttpMethod());
    }

    public function testCreateProfileWithToken()
    {
        $request = $this->gateway->createProfile(
            array(
                'language' => 'test-language',
                'comment' => 'test-comment',
                'billing' => array(
                    'name' => 'test mann',
                    'email_address' => 'testmann@email.com',
                    'address_line1' => '123 Test St',
                    'address_line2' => '',
                    'city' => 'vancouver',
                    'province' => 'bc',
                    'postal_code' => 'H0H0H0',
                    'phone_number' => '1 (555) 555-5555'
                ),
                'token' => array(
                    'name' => 'token-test-name',
                    'code' => 'token-test-code'
                )
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\CreateProfileRequest', $request);
        $this->assertSame('test-language', $request->getLanguage());
        $this->assertSame('test-comment', $request->getComment());
        $this->assertSame(array(
            'name' => 'test mann',
            'email_address' => 'testmann@email.com',
            'address_line1' => '123 Test St',
            'address_line2' => '',
            'city' => 'vancouver',
            'province' => 'bc',
            'postal_code' => 'H0H0H0',
            'phone_number' => '1 (555) 555-5555'
        ), $request->getBilling());
        $this->assertSame(array(
            'name' => 'token-test-name',
            'code' => 'token-test-code'
        ), $request->getToken());
        $this->assertSame('POST', $request->getHttpMethod());
    }

    public function testFetchProfile()
    {
        $request = $this->gateway->fetchProfile(
            array(
                'profile_id' => 1
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\FetchProfileRequest', $request);
        $this->assertSame(1, $request->getProfileId());
        $this->assertSame('GET', $request->getHttpMethod());
    }

    public function testUpdateProfile()
    {
        $request = $this->gateway->updateProfile(
            array(
                'profile_id' => 1,
                'language' => 'test-language',
                'comment' => 'test-comment'
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\UpdateProfileRequest', $request);
        $this->assertSame('test-language', $request->getLanguage());
        $this->assertSame('test-comment', $request->getComment());
        $this->assertSame(1, $request->getProfileId());
        $this->assertSame('PUT', $request->getHttpMethod());
    }

    public function testDeleteProfile()
    {
        $request = $this->gateway->deleteProfile(
            array(
                'profile_id' => 1,
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\DeleteProfileRequest', $request);
        $this->assertSame(1, $request->getProfileId());
        $this->assertSame('DELETE', $request->getHttpMethod());
    }

    public function testCreateProfileCard()
    {
        $request = $this->gateway->createProfileCard(
            array(
                'profile_id' => 1
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\CreateProfileCardRequest', $request);
        $this->assertSame(1, $request->getProfileId());
        $this->assertSame('POST', $request->getHttpMethod());
    }

    public function testFetchProfileCards()
    {
        $request = $this->gateway->fetchProfileCards(
            array(
                'profile_id' => 1
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\FetchProfileCardsRequest', $request);
        $this->assertSame(1, $request->getProfileId());
        $this->assertSame('GET', $request->getHttpMethod());
    }

    public function testUpdateProfileCard()
    {
        $request = $this->gateway->updateProfileCard(
            array(
                'profile_id' => 1,
                'card_id' => 2
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\UpdateProfileCardRequest', $request);
        $this->assertSame(1, $request->getProfileId());
        $this->assertSame(2, $request->getCardId());
        $this->assertSame('PUT', $request->getHttpMethod());
    }

    public function testDeleteProfileCard()
    {
        $request = $this->gateway->deleteProfileCard(
            array(
                'profile_id' => 1,
                'card_id' => 2
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\DeleteProfileCardRequest', $request);
        $this->assertSame(1, $request->getProfileId());
        $this->assertSame(2, $request->getCardId());
        $this->assertSame('DELETE', $request->getHttpMethod());
    }

    /**
     * Test the creation of a RefundRequest object
     */
    public function testRefund()
    {
        $request = $this->gateway->refund(
            array(
                'transactionReference'=>100,
                'amount'=> 10.00
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\RefundRequest', $request);
        $this->assertSame(100, $request->getTransactionReference());
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('POST', $request->getHttpMethod());
        $this->assertSame('https://www.beanstream.com/api/v1/payments/100/returns', $request->getEndpoint());
    }

    /**
     * Test the creation of a VoidRequest object
     */
    public function testVoid()
    {
        $request = $this->gateway->void(
            array(
                'transactionReference'=>100,
                'amount'=> 10.00
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\VoidRequest', $request);
        $this->assertSame(100, $request->getTransactionReference());
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('POST', $request->getHttpMethod());
        $this->assertSame('https://www.beanstream.com/api/v1/payments/100/void', $request->getEndpoint());
    }

    /**
     * Test the creation of a CaptureRequest object
     */
    public function testCapture()
    {
        $request = $this->gateway->capture(
            array(
                'transactionReference'=>100,
                'amount'=>10.00
            )
        );
        $this->assertInstanceOf('Omnipay\Beanstream\Message\CaptureRequest', $request);
        $this->assertSame(100, $request->getTransactionReference());
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('POST', $request->getHttpMethod());
        $this->assertSame('https://www.beanstream.com/api/v1/payments/100/completions', $request->getEndpoint());
    }
}
