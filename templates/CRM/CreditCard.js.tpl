{literal}
// remove spaces, dashes from credit card number
cj('#credit_card_number').change(function(){
    var cc = cj('#credit_card_number').val()
        .replace(/ /g, '')
        .replace(/-/g, '');
    cj('#credit_card_number').val(cc);
});
{/literal}
