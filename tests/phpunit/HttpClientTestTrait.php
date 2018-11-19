<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;
use Omnipay\Common\Http\Client;
use Http\Mock\Client as MockClient;

/**
 * Class GuzzleTestTrait
 *
 * This trait defines a number of helper functions for testing guzzle.
 *
 * So far it's experimental - trying to figure out what helpers are most
 * useful but later it might be upstreamed.
 *
 *
 * This trait is intended for use with PHPUnit-based test cases.
 */
trait HttpClientTestTrait {

  /** @var  MockClient */
  private $mockClient;

  /** @var Client */
  private $httpClient;

  /**
   * @var MockHandler
   */
  protected $mockHandler;

  /**
   * @var string
   */
  protected $baseUri;

  /**
   * Get the test client that we will use in our tests.
   *
   * @return \Omnipay\Common\Http\Client
   */
  public function getHttpClient() {
    if (null === $this->httpClient) {
      $this->httpClient = new Client(
        $this->getMockClient()
      );
    }

    return $this->httpClient;
  }

  /**
   * Get the mock client to use in our http client.
   *
   * @return \Http\Mock\Client
   */
  public function getMockClient() {
    if (null === $this->mockClient) {
      $this->mockClient = new MockClient();
    }

    return $this->mockClient;
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
   * @return void returns the created mock plugin
   */
  public function setMockHttpResponse($paths)
  {
    foreach ((array) $paths as $path) {
      $this->getMockClient()->addResponse($this->getMockHttpResponse($path));
    }
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
   * @param array $output
   *   Array to be used for the returned response.
   *
   * @return void returns the created mock plugin
   */
  public function setMockHttpResponseToArray($output) {
    $this->getMockClient()->addResponse(new Response(200, [], http_build_query($output)));
  }

  /**
   * Get a mock response for a client by mock file name
   *
   * @param string $path Relative path to the mock response file
   *
   * @return ResponseInterface
   */
  public function getMockHttpResponse($path)
  {
    $ref = new ReflectionObject($this);
    $dir = dirname($ref->getFileName());
    return \GuzzleHttp\Psr7\parse_response(file_get_contents($dir . '/Mock/' . $path));
  }

  /**
   * @return mixed
   */
  public function getBaseUri() {
    return $this->baseUri;
  }

  /**
   * @param mixed $baseUri
   */
  public function setBaseUri($baseUri) {
    $this->baseUri = $baseUri;
  }

  /**
   * Get the bodies of the requests sent via Guzzle.
   *
   * @return array
   */
  protected function getRequestBodies() {
    $requests = [];
    foreach ($this->mockClient->getRequests() as $request) {
      $requests[] = (string) $request->getBody();
    }
    return $requests;
  }

}
