<?php

use Civi\Test\Api3TestTrait;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\Invasive;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the success URL the browser is sent back to after payment.
 *
 * The entity id has to survive that round trip. Browsers routinely drop the
 * session cookie on the way back from an external gateway (SameSite=Lax,
 * Safari ITP), and without an id in the URL the ThankYou page then fails with
 * "Could not find valid value for id" even though the payment succeeded.
 *
 * @group headless
 */
class ReturnSuccessUrlTest extends TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  use Api3TestTrait;
  use OmnipayTestTrait;

  /**
   * ID of payment processor created for test.
   *
   * @var int
   */
  protected $paymentProcessorID;

  /**
   * @return \Civi\Test\CiviEnvBuilder
   * @throws \CRM_Extension_Exception_ParseException
   */
  public function setUpHeadless(): \Civi\Test\CiviEnvBuilder {
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp(): void {
    parent::setUp();
    $this->paymentProcessorID = (int) $this->createTestProcessor('Mercanet_Offsite')['id'];
  }

  /**
   * @return \CRM_Core_Payment_OmnipayMultiProcessor
   */
  protected function getProcessor() {
    return \Civi\Payment\System::singleton()->getById($this->paymentProcessorID);
  }

  /**
   * A known entity id ends up in the URL, so the ThankYou page can find it
   * without depending on the session.
   */
  public function testEntityIdIsAddedToTheUrl(): void {
    $processor = $this->getProcessor();
    Invasive::set([$processor, '_component'], 'event');
    $url = Invasive::call([$processor, 'getReturnSuccessUrl'], ['qf_key_1', NULL, 42]);

    $this->assertStringContainsString('_qf_ThankYou_display=1', $url);
    $this->assertStringContainsString('qfKey=qf_key_1', $url);
    $this->assertStringContainsString('id=42', $url);
  }

  /**
   * With only a participant id the event id is looked up, mirroring core.
   */
  public function testEventIdIsResolvedFromTheParticipant(): void {
    $contactID = $this->callAPISuccess('Contact', 'create', [
      'contact_type' => 'Individual',
      'first_name' => 'Return',
      'last_name' => 'Url',
    ])['id'];
    $eventID = $this->callAPISuccess('Event', 'create', [
      'title' => 'Return url event',
      'event_type_id' => 1,
      'start_date' => '2026-01-01',
    ])['id'];
    $participantID = $this->callAPISuccess('Participant', 'create', [
      'contact_id' => $contactID,
      'event_id' => $eventID,
    ])['id'];

    $processor = $this->getProcessor();
    Invasive::set([$processor, '_component'], 'event');
    $url = Invasive::call([$processor, 'getReturnSuccessUrl'], ['qf_key_2', $participantID, NULL]);

    $this->assertStringContainsString('id=' . $eventID, $url);
  }

  /**
   * Without any id the URL is what it always was - no empty id parameter.
   */
  public function testUrlIsUnchangedWhenNoIdIsKnown(): void {
    $processor = $this->getProcessor();
    Invasive::set([$processor, '_component'], 'contribute');
    $url = Invasive::call([$processor, 'getReturnSuccessUrl'], ['qf_key_3']);

    $this->assertStringContainsString('_qf_ThankYou_display=1', $url);
    $this->assertStringContainsString('qfKey=qf_key_3', $url);
    $this->assertStringNotContainsString('id=', str_replace('_qf_ThankYou_display', '', $url));
  }

  /**
   * An explicitly set success URL still wins over everything else.
   */
  public function testExplicitSuccessUrlWins(): void {
    $processor = $this->getProcessor();
    $processor->setSuccessUrl('https://example.org/thanks');
    $url = Invasive::call([$processor, 'getReturnSuccessUrl'], ['qf_key_4', NULL, 42]);

    $this->assertEquals('https://example.org/thanks', $url);
  }

}
