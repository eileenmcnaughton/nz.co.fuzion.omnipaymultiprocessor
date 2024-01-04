<?php


namespace Civi\OmnipayMultiProcessor\ActionProvider;

use Civi\ActionProvider\Action\AbstractAction;
use Civi\ActionProvider\Exception\ExecutionException;
use Civi\ActionProvider\Parameter\OptionGroupSpecification;
use Civi\ActionProvider\Parameter\ParameterBagInterface;
use Civi\ActionProvider\Parameter\Specification;
use Civi\ActionProvider\Parameter\SpecificationBag;
use Civi\API\Exception\UnauthorizedException;
use Civi\Api4\PaymentProcessor;
use CRM_Core_Exception;
use CRM_Omnipaymultiprocessor_ExtensionUtil as E;

class DoOnlinePayment extends AbstractAction {

  /**
   * @var array
   */
  protected $paymentProcessors;

  /**
   * Run the action
   *
   * @param ParameterBagInterface $parameters
   *   The parameters to this action.
   * @param ParameterBagInterface $output
   * 	 The parameters this action can send back
   * @return void
   * @throws \Exception
   */
  protected function doAction(ParameterBagInterface $parameters, ParameterBagInterface $output) {
    $paymentParams['contribution_id'] = $parameters->getParameter('contribution_id');
    $paymentParams['amount'] = (float) $parameters->getParameter('total_amount');
    $paymentParams['currency'] = $parameters->getParameter('currency');
    $paymentParams['description'] = $parameters->getParameter('description');
    $successUrl = $parameters->getParameter('success_url');
    $cancelurl = $parameters->getParameter('cancel_url');

    $paymentProcessor = $this->getPaymentProcessorByName($this->configuration->getParameter('payment_processor'));
    if (!$paymentProcessor) {
      throw new ExecutionException('Invalid Payment Processor');
    }
    $payment = \Civi\Payment\System::singleton()->getByProcessor($paymentProcessor);
    if ($payment instanceof \CRM_Core_Payment_PaymentExtended) {
      $payment->setReturnUrl($successUrl);
    }
    $payment->setBaseReturnUrl($successUrl);
    $payment->setSuccessUrl($successUrl);
    $payment->setCancelUrl($cancelurl);
    $result = $payment->doPreApproval($paymentParams);
    $output->setParameter('redirect_url', $result['redirect_url']);
  }

  /**
   * @return \Civi\ActionProvider\Parameter\SpecificationBag
   */
  public function getOutputSpecification() {
    return new SpecificationBag(array(
      new Specification('redirect_url', 'String', E::ts('Redirect URL'), false),
    ));
  }

  /**
   * Returns the specification of the configuration options for the actual action.
   *
   * @return SpecificationBag
   */
  public function getConfigurationSpecification() {
    $paymentProcessorOptions = [];
    foreach($this->getPaymentProcessors() as $paymentProcessor) {
      $paymentProcessorOptions[$paymentProcessor['name']] = $paymentProcessor['title'];
    }
    return new SpecificationBag(array(
      new Specification('payment_processor', 'String', E::ts('Payment Processor'), TRUE, null, null, $paymentProcessorOptions),
    ));
  }

  /**
   * Returns the specification of the parameters of the actual action.
   *
   * @return SpecificationBag
   */
  public function getParameterSpecification() {
    return new SpecificationBag([
      new Specification('contribution_id', 'Integer', E::ts('Contribution ID'), TRUE),
      new Specification('total_amount', 'Float', E::ts('Amount'), TRUE),
      new OptionGroupSpecification('currency', 'currencies_enabled', E::ts('Currency'), TRUE),
      new Specification('success_url', 'String', E::ts('Success URL'), TRUE),
      new Specification('cancel_url', 'String', E::ts('Cancel URL'), TRUE),
      new Specification('description', 'String', E::ts('Payment Description'), TRUE),
    ]);
  }

  public function getHelpText() {
    return E::ts('This action creates an online payment at a payment processor and returns the url at which the user can finish the payment');
  }

  private function getPaymentProcessors() {
    if (empty($this->paymentProcessors)) {
      try {
        $this->paymentProcessors = PaymentProcessor::get(FALSE)
          ->addWhere('is_active', '=', TRUE)
          ->execute()
          ->getArrayCopy();
      }
      catch (UnauthorizedException|CRM_Core_Exception $e) {
      }
    }
    return $this->paymentProcessors;
  }

  private function getPaymentProcessorByName(string $name):? array {
    foreach($this->getPaymentProcessors() as $paymentProcessor) {
      if ($paymentProcessor['name'] == $name) {
        return $paymentProcessor;
      }
    }
    return null;
  }

}
