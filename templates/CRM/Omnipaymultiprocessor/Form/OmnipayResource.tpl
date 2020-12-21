{*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
*}
{* Manually create the CRM.vars.omnipay here for cases where \Civi::resources()->addVars() does not work (eg. drupal webform_civicrm, contribution page paypal checkout is default *}
{literal}
<script type="text/javascript">
  CRM.$(function($) {
    $(document).ready(function() {
      if (typeof CRM.vars.omnipay === 'undefined') {
        var omnipay = {{/literal}{foreach from=$omnipayJSVars key=arrayKey item=arrayValue}{$arrayKey}:'{$arrayValue}',{/foreach}{literal}};
        CRM.vars.omnipay = omnipay;
        //console.log('added vars via tpl');
      }
    });
  });
</script>
{/literal}
