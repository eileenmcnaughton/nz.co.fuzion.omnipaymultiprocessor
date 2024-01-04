<?php

require_once 'omnipaymultiprocessor.civix.php';
use CRM_Omnipaymultiprocessor_ExtensionUtil as E;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use \Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Implements hook_civicrm_container()
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_container/
 */
function omnipaymultiprocessor_civicrm_container(ContainerBuilder $container) {
  $container->addCompilerPass(new Civi\OmnipayMultiProcessor\ActionProvider\CompilerPass(), PassConfig::TYPE_OPTIMIZE);
}


/**
 * Implementation of hook_civicrm_config
 *
 * @param $config
 */
function omnipaymultiprocessor_civicrm_config($config) {
  _omnipaymultiprocessor_civix_civicrm_config($config);
  $extRoot = __DIR__ . DIRECTORY_SEPARATOR;
  $include_path = $extRoot . DIRECTORY_SEPARATOR . 'vendor' . PATH_SEPARATOR . get_include_path( );
  set_include_path( $include_path );
  require_once 'vendor/autoload.php';
}

/**
 * Implementation of hook_civicrm_install
 */
function omnipaymultiprocessor_civicrm_install() {
  _omnipaymultiprocessor_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_enable
 */
function omnipaymultiprocessor_civicrm_enable() {
  _omnipaymultiprocessor_civix_civicrm_enable();
}

/**
 * @param string $entity
 * @param string $action
 * @param array $params
 * @param array $permissions
 */
function omnipaymultiprocessor_civicrm_alterAPIPermissions(string $entity, $action, $params, &$permissions) {
  $permissions['payment_processor']['preapprove'] = ['make online contributions'];
}

/**
 * Implements hook_alterMenu().
 *
 * @param array $items
 */
function omnipaymultiprocessor_civicrm_alterMenu(&$items) {
  $items['civicrm/ajax/rest']['page_callback'] = ['CRM_Utils_RestPreapprove', 'ajax'];
  $items['civicrm/ajax/rest']['access_arguments'][0][] = 'make online contributions';
}

function omnipaymultiprocessor_civicrm_navigationMenu(&$menu) {
  _omnipaymultiprocessor_civix_insert_navigation_menu($menu, 'Administer/System Settings', [
    'label' => E::ts('Omnipay Developer Settings'),
    'name' => 'omnpay-dev',
    'url' => 'civicrm/settings/omnipay-dev',
    'permission' => 'administer payment processors',

  ]);
}

function omnipaymultiprocessor_civicrm_preProcess($formName, $form) {
  if ($formName === 'CRM_Contribute_Form_Contribution_Main') {
    $form->assign('isJsValidate', TRUE);
    if (!empty($form->_values['is_recur'])) {
      $recurOptions = [
        'is_recur_interval' =>  $form->_values['is_recur_interval'],
        'frequency_unit' => $form->_values['recur_frequency_unit'],
        'is_recur_installments' => $form->_values['is_recur_installments'],
      ];

      if (!$recurOptions['is_recur_interval']) {
        $recurOptions['frequency_interval'] = 1;
      }
     CRM_Core_Resources::singleton()->addVars(
        'omnipay', $recurOptions
     );
    }
  }

}

function omnipaymultiprocessor_civicrm_alterPaymentProcessorParams($instance, $params, &$creditCardOptions){
  if($instance instanceof CRM_Core_Payment_OmnipayMultiProcessor ) {
    $payment_processor_id = $params['payment_processor_id'];

    $payment_processor_types = civicrm_api4('PaymentProcessor', 'get', [
      'select' => [
        'payment_processor_type_id.name',
      ],
      'where' => [
        ['id', '=', $payment_processor_id],
      ],
      'limit' => 1,
      'checkPermissions' => false,
    ]);
    $payment_processor_type_name = $payment_processor_types->first()['payment_processor_type_id.name'];

    // Custom data tranformation for recurring contributions in SystemPay
    if($payment_processor_type_name === 'omnipay_SystemPay' && isset($creditCardOptions['token']) && $creditCardOptions['token']){
      $rawTokenData = $creditCardOptions['token'];
      if(empty($rawTokenData['contributionRecurID'])){
        return;
      }
      $creditCardOptions['token'] = [];
      $creditCardOptions['token']['vads_page_action'] = 'REGISTER_PAY_SUBSCRIBE';
      $creditCardOptions['token']['vads_sub_amount'] = $creditCardOptions['amount']*100;
      $creditCardOptions['token']['vads_sub_currency'] = $creditCardOptions['currency'];
      $creditCardOptions['token']['vads_subscription'] = $rawTokenData['contributionRecurID'];
      
      // Next payment should occur after 1 interval
      $creditCardOptions['token']['vads_sub_effect_date'] = date('Ymd', strtotime('+'.$rawTokenData['frequencyInterval'].' '.$rawTokenData['frequencyUnit']));
      
      // Manage frequency
      $creditCardOptions['token']['vads_sub_desc'] = 'RRULE:FREQ='.(strtoupper($rawTokenData['frequencyUnit']=='day'?'dai':$rawTokenData['frequencyUnit']).'LY').';INTERVAL='.$rawTokenData['frequencyInterval'];

      // TODO: implement this option
      // $creditCardOptions['token']['vads_sub_init_amount'] = null;
      // $creditCardOptions['token']['vads_sub_init_amount_number'] = null;

      if(isset($rawTokenData['token'])){
        $creditCardOptions['token'] = array_merge($creditCardOptions['token'], $rawTokenData['token']);
      }
    }
  }
}