<?php

namespace Omnipay\NABTransact\Enums;

class TransactionType
{
    const NORMAL_PAYMENT = 0;
    const NORMAL_PREAUTH = 1;

    const PAYMENT_RISK_MANAGEMENT = 2;
    const PREAUTH_RISK_MANAGEMENT = 3;

    const PAYMENT_3DS_EMV3DS = 4;
    const PREAUTH_3DS_EMV3DS = 5;

    const PAYMENT_RISK_MANAGEMENT_3DS_EMV3DS = 6;
    const PREAUTH_RISK_MANAGEMENT_3DS_EMV3DS = 7;

    const STORE_ONLY = 8;
}
