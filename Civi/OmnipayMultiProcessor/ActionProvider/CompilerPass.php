<?php

namespace Civi\OmnipayMultiProcessor\ActionProvider;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use CRM_Omnipaymultiprocessor_ExtensionUtil as E;

class CompilerPass implements CompilerPassInterface {

  public function process(ContainerBuilder $container) {
    if ($container->hasDefinition('action_provider')) {
      $actionProviderDefinition = $container->getDefinition('action_provider');
      $actionProviderDefinition->addMethodCall('addAction',
        [
          'OmnipayMultiProcessor_OnlinePayment',
          'Civi\OmnipayMultiProcessor\ActionProvider\DoOnlinePayment',
          E::ts('Contribution: Do Online Payment with Omnipay Multi Processor'),
          []
        ]);
    }

  }

}
