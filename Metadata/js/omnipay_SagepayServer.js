CRM.$(function($) {
  modifyBillingFields = function() {
    if ($('#billing_country_id-5').val() == '1105') { // Ireland
      $('.billing_postal_code-5-section').hide();
      $('.billing_postal_code-5-section input').val('0000');
    }
    else {
      if ($('.billing_postal_code-5-section:hidden').length !== 0) {
        $('.billing_postal_code-5-section input').val('');
        $('.billing_postal_code-5-section').show();
      }
      // Add note about postcode field
      $('div.billing_postal_code-5-section div.content div.description').remove();
      if ($('#billing_country_id-5').val() != '1226') { // United Kingdom
        $('div.billing_postal_code-5-section div.content').append(
          '<div class="description">' + ts('If your address does not have a postal code please enter 0000 in this field.') + '</div>'
        );
      }
    }
  };

  $('#billing_country_id-5').on('change', function () {
    modifyBillingFields()
  });
  modifyBillingFields();
});
