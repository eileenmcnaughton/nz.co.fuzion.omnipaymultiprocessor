<?php

define ('HMAC_SHA256', 'sha256');
define ('SECRET_KEY', 'dc3a5bfebd894d209ebe18cbc9a5431d26a3964124584cc58b883473cac066dc02d5abbc04864023a0b2414c281a95bf0345b2c9cf884046b857c5cc77a58eff31ff74fb76a144e39696c061676f0088de6713b837444710ae833b0d223c8bceecb3abe8fcd641daa73fd91b2f683d0fe8867247b64348b4a08bc60430bfde2e');

function sign ($params) {
  return signData(buildDataToSign($params), SECRET_KEY);
}

function signData($data, $secretKey) {
    return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
}

function buildDataToSign($params) {
        $signedFieldNames = explode(",",$params["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
           $dataToSign[] = $field . "=" . $params[$field];
        }
        return commaSeparate($dataToSign);
}

function commaSeparate ($dataToSign) {
    return implode(",",$dataToSign);
}

?>
