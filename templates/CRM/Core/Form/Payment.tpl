{$post_url}
<form action="{$post_url}" method="post">
  {foreach from=$hidden_fields value=hidden_field item=hidden_field_input}
    <imput name="{$hidden_field}" value="{$hidden_field_input}" type="hidden"></imput>
  {/foreach}
  {foreach from=$display_fields item=display_field value=field_spec}
    {assign var='core_field_name' value=$field_spec.core_field_name}
    <div class="crm-section {$form.$core_field_name.name}-section">
      <div class="label">{$form.$display_field.label} {$reqMark}</div>
      <div class="content">{$form.$display_field.html}</div>
      <div class="clear"></div>
    </div>
  {/foreach}

  <input type="submit" value="{ts}Submit{/ts}">

</form>
<script>
  {include file='CRM/CreditCard.js.tpl'}
</script>
