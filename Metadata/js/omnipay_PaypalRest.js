// @see https://developer.paypal.com/docs/checkout/integrate/
(function($) {
  var form = $('#billing-payment-block').closest('form');
  var qfKey = $('[name=qfKey]', form).val();

  if (typeof CRM.vars.omnipay === 'undefined') {
    console.log('CRM.vars.omnipay not defined! Not a Omnipay processor?');
    return;
  }

  function renderPaypal() {
    paypal.Buttons({

        onInit: function(data, actions) {
          // Set up the buttons.
          if (form.valid()) {
            actions.enable();
          }
          else {
            actions.disable();
          }

          form.on('blur keyup change', 'input', function (event) {
            if (form.valid()) {
              actions.enable();
            }
            else {
              actions.disable();
            }
          });
        },

        createBillingAgreement: function (data, actions) {

          // CRM.payment.getTotalAmount is implemented by webform_civicrm and mjwshared. The plan is to
          //   add CRM.payment.getTotalAmount() into CiviCRM core. This code allows it to work under any of
          //   these circumstances as well as if CRM.payment does not exist.
          var totalAmount = 0.0;
          if ((typeof CRM.payment !== 'undefined') && (CRM.payment.hasOwnProperty('getTotalAmount'))) {
            totalAmount = CRM.payment.getTotalAmount();
          }
          else if (typeof calculateTotalFee == 'function') {
            // This is ONLY triggered in the following circumstances on a CiviCRM contribution page:
            // - With a priceset that allows a 0 amount to be selected.
            // - When we are the ONLY payment processor configured on the page.
            totalAmount = parseFloat(calculateTotalFee());
          }
          else if (document.getElementById('total_amount')) {
            // The input#total_amount field exists on backend contribution forms
            totalAmount = parseFloat(document.getElementById('total_amount').value);
          }

          var frequencyInterval = $('#frequency_interval').val() || 1;
          var frequencyUnit = $('#frequency_unit').val() ? $('#frequency_interval').val() : CRM.vars.omnipay.frequency_unit;
          var isRecur = $('#is_recur').is(":checked");
          var recurText = isRecur ? ' recurring' : '';

          return new Promise(function (resolve, reject) {
            CRM.api3('PaymentProcessor', 'preapprove', {
                'payment_processor_id': CRM.vars.omnipay.paymentProcessorId,
                'amount': totalAmount,
                'currencyID' : CRM.vars.omnipay.currency,
                'qf_key': qfKey,
                'is_recur' : isRecur,
                'installments' : $('#installments').val(),
                'frequency_unit' : frequencyUnit,
                'frequency_interval' : frequencyInterval,
                'description' : CRM.vars.omnipay.title + ' ' + CRM.formatMoney(totalAmount) + recurText,
              }
            ).then(function (result) {
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

        onApprove: function (data, actions) {
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
          form.submit();
        },

        onError: function(err) {
          console.log(err);
          alert('Site is not correctly configured to process payments');
        }

      })
      .render('#paypal-button-container');
  }

  var paypalScriptURL = 'https://www.paypal.com/sdk/js?client-id=' + CRM.vars.omnipay.client_id + '&currency=' + CRM.vars.omnipay.currency + '&commit=false&vault=true';
  CRM.loadScript(paypalScriptURL, false).done(renderPaypal);


})(CRM.$);
