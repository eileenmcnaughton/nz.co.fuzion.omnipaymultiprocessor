<?php
/**
 * Created by IntelliJ IDEA.
 * User: emcnaughton
 * Date: 2/16/18
 * Time: 12:18 PM
 */

namespace Omnipay\Mercanet\Message;

use Omnipay\Tests\TestCase;

class ResponseTest extends TestCase {
    public function testDirectPurchaseWithToken()
    {
        $httpResponse = $this->getMockHttpResponse('DirectPurchaseWithToken.txt');
        $response = new Response($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('{ABCDEFGH-ABCD-ABCD-ABCD-ABCDEFGHIJKL}', $response->getToken());
    }
}
