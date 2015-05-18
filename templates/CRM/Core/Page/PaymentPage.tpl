<form action="{$post_url}" method="post">
  {foreach from=$hidden_fields key=hidden_field item=hidden_field_input}
    <input name="{$hidden_field}" value="{$hidden_field_input}" type="hidden"/>
  {/foreach}
  {foreach from=$display_fields key=display_field item=field_spec}
    {assign var='core_field_name' value=$field_spec.core_field_name}
    <div class="crm-section {$core_field_name}-section">
      <div class="label">{ts}{$field_spec.title}{/ts} <span class="crm-marker" title="This field is required.">*</span></div>
      <div class="content">
      {if $field_spec.htmlType == 'text'}
        <input name="{$display_field}" type="text" id="{$core_field_name}" class="medium crm-form-text required"
          {foreach from=$field_spec.options key=attribute item=option_value}
            {$attribute} = "{$option_value}"
          {/foreach}
        >
        {* this is a hack in core & it's a hack here... *}
        {if $core_field_name == 'cvv2'}
          <span class="cvv2-icon" title="{ts}Usually the last 3-4 digits in the signature area on the back of the card.{/ts}"> </span>
        {/if}

     {elseif $field_spec.htmlType == 'select'}
        <select name="{$display_field}" id="{$core_field_name}" class="crm-form-select">
          {foreach from=$field_spec.options key=attribute item=attribute_value}
            <option value="{$attribute}">{$attribute_value}</option>
          {/foreach}
        </select>
      {elseif $field_spec.htmlType == 'date'}
        {*  @todo - fix this hard-coding hack *}
        <select class="crm-form-date required" id="{$display_field}_M" name="card_expiry_date[M]">
          <option value="">-month-</option>
          <option value="01">Jan</option>
          <option value="02">Feb</option>
          <option value="03">Mar</option>
          <option value="04">Apr</option>
          <option value="05">May</option>
          <option value="06">Jun</option>
          <option value="07">Jul</option>
          <option value="08">Aug</option>
          <option value="09">Sep</option>
          <option value="10">Oct</option>
          <option value="11">Nov</option>
          <option value="12">Dec</option>
        </select>&nbsp;
        <select class="crm-form-date required" id="{$display_field}_Y" name="card_expiry_date[Y]">
          <option value="">-year-</option>
          {foreach from=$field_spec.options.year item=year}
            <option value="{$year}">{$year}</option>
          {/foreach}
        </select>
        <input name="{$display_field}" id="{$display_field}" type='hidden' value=""/>
        <script>

          // remove spaces, dashes from credit card number
          cj('#{$display_field}_Y, #{$display_field}_M').change(function(){literal}{{/literal}
            cj('#{$display_field}').val(cj('#{$display_field}_M').val() + '-' + cj('#{$display_field}_Y').val())
            {literal}
          });
          {/literal}
          {include file='CRM/CreditCard.js.tpl'}
        </script>
      {/if}
      </div>
      <div class="clear"></div>
    </div>
  {/foreach}

  <input class='form-submit default crm-form-submit' type="submit" value="{ts}Submit{/ts}">

</form>

