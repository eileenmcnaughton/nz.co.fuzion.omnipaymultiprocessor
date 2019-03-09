
// https://developer.paypal.com/docs/integration/direct/express-checkout/integration-jsv4/upgrade-integration/
// https://developer.paypal.com/docs/integration/direct/express-checkout/integration-jsv4/add-paypal-button/
var formID = CRM.$('#billing-payment-block').closest('form').attr('id');
var qfKey = CRM.$('#' + formID + ' [name=qfKey]').val();

renderPaypal = function() {
  paypal.Button.render({
    env: (CRM.vars.omnipay.is_test ? 'sandbox' : 'production'),
    style: {layout: 'vertical', 'size': 'responsive'},
    funding: {disallowed: [paypal.FUNDING.CREDIT]},
    payment: function (data, actions) {

      var frequencyInterval = CRM.$('#frequency_interval').val() ? CRM.$('#frequency_interval').val() : 1;
      var frequencyUnit = CRM.$('#frequency_unit').val() ? CRM.$('#frequency_interval').val() : CRM.vars.omnipay.frequency_unit;

      return new paypal.Promise(function (resolve, reject) {
        var paymentAmount = calculateTotalFee();
        var isRecur = CRM.$('#is_recur').is(":checked");
        var recurText = isRecur ? ' recurring' : '';
        CRM.api3('PaymentProcessor', 'preapprove', {
            'payment_processor_id': CRM.vars.omnipay.paymentProcessorId,
            'amount': paymentAmount,
            'currencyID' : CRM.vars.omnipay.currency,
             'qf_key': qfKey,
             'is_recur' : isRecur,
             'installments' : CRM.$('#installments').val(),
             'frequency_unit' : frequencyUnit,
             'frequency_interval' : frequencyInterval,
             'description' : CRM.vars.omnipay.title + ' ' + CRM.formatMoney(paymentAmount) + recurText
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
      var isRecur = 1;
      var paymentToken = data['billingToken'];
      if (!paymentToken) {
        paymentToken = data['paymentID'];
        isRecur = 0;
      }

      document.getElementById('paypal-button-container').style.visibility = "hidden";
      document.getElementById('crm-submit-buttons').style.display = 'block';
      document.getElementById('PayerID').value = data['payerID'];
      document.getElementById('payment_token').value = paymentToken;
      document.getElementById(formID).submit();
    },

    onError: function(err) {
      console.log(err);
      alert('Site is not correctly configured to process payments');
    }

  }, '#paypal-button-container');
};

if (typeof paypal === "undefined") {
  CRM.$.getScript('https://www.paypalobjects.com/api/checkout.js', function() {
      renderPaypal();
  });
}
else {
  renderPaypal();
}



