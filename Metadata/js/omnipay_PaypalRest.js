
// https://developer.paypal.com/docs/integration/direct/express-checkout/integration-jsv4/upgrade-integration/
// https://developer.paypal.com/docs/integration/direct/express-checkout/integration-jsv4/add-paypal-button/
var formID = CRM.$('#billing-payment-block').closest('form').attr('id');
var qfKey = CRM.$('#' + formID + ' [name=qfKey]').val();

renderPaypal = function() {
  paypal.Button.render({
    env: (CRM.vars.omnipay.is_test ? 'sandbox' : 'production'),
    payment: function (data, actions) {
      return new paypal.Promise(function (resolve, reject) {
        CRM.api3('PaymentProcessor', 'preapprove', {
            'payment_processor_id': CRM.vars.omnipay.paymentProcessorId,
            'amount': calculateTotalFee(),
            'currencyID' : CRM.vars.omnipay.currency,
             'qf_key': qfKey,
             'is_recur' : CRM.$('#is_recur').is(":checked"),
             'installments' : CRM.$('#installments').val(),
             'frequency_unit' : CRM.$('#frequency_unit').val(),
             'frequency_interval' : CRM.$('#frequency_interval').val()
          }
        ).done(function (result) {
          if (result['is_error'] === 1) {
            reject(result['error_message']);
          }
          else {
            token = result['values'][0]['token'];
            resolve(token);
          }
        })
          .fail(function (result) {
            reject('Payment failed. Check your site credentials');
          });
      });
    },

    onAuthorize: function (data, actions) {
      document.getElementById('paypal-button').style.visibility = "hidden";
      document.getElementById('crm-submit-buttons').style.display = 'block';
      document.getElementById('PayerID').value = data['payerID'];
      document.getElementById('payment_token').value = data['paymentID'];
      document.getElementById(formID).submit();
    },

    onError: function(err) {
      console.log(err);
      alert('Site is not correctly configured to process payments');
    }

  }, '#paypal-button');
};

if (typeof paypal === "undefined") {
  CRM.$.getScript('https://www.paypalobjects.com/api/checkout.js', function() {
      renderPaypal();
  });
}
else {
  renderPaypal();
}



