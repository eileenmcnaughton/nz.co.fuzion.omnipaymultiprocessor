// @see https://developer-eu.elavon.com/docs/opayo-server/api-reference/transaction-registration
(function ($) {
  // add validation for better user experience
  const fields = {
    first_name: { maxlength: 20 },
    last_name: { maxlength: 20 },
    billing_first_name: { maxlength: 20 },
    billing_last_name: { maxlength: 20 },
    street_address: { maxlength: 50 },
    billing_street_address: { maxlength: 50 },
    city: { maxlength: 40 },
    billing_city: { maxlength: 40 },
    postal_code: { maxlength: 10 },
    billing_postal_code: { maxlength: 10 },
  };

  // set the maxlength for the fields to respect SagePay's maxlength
  $.each(fields, function (key, value) {
    var elementSelector = $("[id^=" + key + "]");
    // let's add only if element exists
    if (elementSelector.length) {
      elementSelector.attr("maxlength", value.maxlength);
    }
  });
})(CRM.$);
