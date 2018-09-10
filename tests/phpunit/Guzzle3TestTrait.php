<?php

use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\Service\Client;

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
trait Guzzle3TestTrait {

  /**
   * @var MockHandler
   */
  protected $mockHandler;

  /**
   * @var string
   */
  protected $baseUri;

  /** Array containing guzzle history of requests and responses.
   *
   * @var array
   */
  protected $container;

  /**
   * @return array
   */
  public function getContainer() {
    return $this->container;
  }

  /**
   * @param array $container
   */
  public function setContainer($container) {
    $this->container = $container;
  }

  /**
   * @return \GuzzleHttp\Client
   */
  public function getGuzzleClient() {
    return $this->guzzleClient;
  }

  /**
   * @param \GuzzleHttp\Client $guzzleClient
   */
  public function setGuzzleClient($guzzleClient) {
    $this->guzzleClient = $guzzleClient;
  }

  /**
   * @var Client
   */
  protected $guzzleClient;

  /**
   * @var HistoryPlugin
   */
  protected $history;

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
   * @return \GuzzleHttp\Handler\MockHandler
   */
  public function getMockHandler() {
    return $this->mockHandler;
  }

  /**
   * @param \GuzzleHttp\Handler\MockHandler $mockHandler
   */
  public function setMockHandler($mockHandler) {
    $this->mockHandler = $mockHandler;
  }

  /**
   * @param $responses
   */
  protected function createMockHandler($responses) {
    $mocks = [];
    foreach ($responses as $response) {
      $mocks[] = new Response(200, [], $response);
    }
    $this->setMockHandler(new MockHandler($mocks));
  }

  /**
   * @param $files
   */
  protected function createMockHandlerForFiles($files) {
    $body = [];
    foreach ($files as $file) {
      $body[] = trim(file_get_contents(__DIR__ . $file));
    }
    $this->createMockHandler($body);
  }

  /**
   * Set up a guzzle client with a history container.
   *
   * After you have run the requests you can inspect $this->container
   * for the outgoing requests and incoming responses.
   *
   * If $this->mock is defined then no outgoing http calls will be made
   * and the responses configured on the handler will be returned instead
   * of replies from a remote provider.
   */
  protected function setUpClientWithHistoryContainer() {
    $this->guzzleClient = new Client();

    // Create a history plugin and attach it to the client
    $this->history = new HistoryPlugin();
    $this->guzzleClient->addSubscriber($this->history);
    Civi::$statics['Omnipay_Test_Config'] = ['client' =>  $this->guzzleClient];
  }

  /**
   * Get the bodies of the requests sent via Guzzle.
   *
   * @return array
   */
  protected function getRequestBodies() {
    $transactions= $this->history->getAll();
    $requests = [];
    foreach ($transactions as $transaction) {
      $requests[] = (string) $transaction['request'];
    }
    return $requests;
  }

  /**
   * Get the bodies of the responses returned via Guzzle.
   *
   * @return array
   */
  protected function getResponseBodies() {
    $responses = [];
    $transactions= $this->history->getAll();
    foreach ($transactions as $transaction) {
      $responses[] = (string) $transaction['response'];
    }
    return $responses;
  }

  protected function assertResponsesOk() {
    $transactions= $this->history->getAll();
    foreach ($transactions as $transaction) {
      $this->assertTRUE(in_array($transaction['response']->getStatusCode(), [200, 201]), 'Failed request' . (string) $transaction['response']->getReasonPhrase() . ' ' . (string) $transaction['response'] . "request was " . $transaction['request']->getUrl() . " " . (string) $transaction['request']);
    }
  }

}
