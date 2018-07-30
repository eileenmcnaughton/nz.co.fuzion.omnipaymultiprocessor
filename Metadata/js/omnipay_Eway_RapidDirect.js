/**
 * Encrypt confidential data fields before the form is submitted.
 *
 * @param field
 * @param string apiKey
 */
function encryptField(field, apiKey) {
  var existingValue = field.val();
  if (existingValue == "") {
    return;
  }
  if (isFieldEncrypted(field)) {
    return;
  }
  field.val(eCrypt.encryptValue(existingValue, apiKey));
}

/**
 * Hide fields that have already been encrypted.
 */
function hideEncryptedFields(field) {
  if (!isFieldEncrypted(field)) {
    return;
  }
  field.hide();
}

/**
 * Is the field already encrypted.
 *
 * @param field
 */
function isFieldEncrypted(field) {
  if (field.length == 0) {
    return;
  }
  var existingValue = field.val();
  if (existingValue.length != 0 && existingValue.substr(0, 9) == 'eCrypted:') {
    return true;
  }
  return false;
}

hideEncryptedFields(cj('#credit_card_number'));
hideEncryptedFields(cj('#cvv2'));

CRM.$('#billing-payment-block').closest('form').submit(function() {
    encryptField(cj('#credit_card_number'), CRM.vars.omnipay.ewayKey);
    encryptField(cj('#cvv2'), CRM.vars.omnipay.ewayKey);
  }
);
