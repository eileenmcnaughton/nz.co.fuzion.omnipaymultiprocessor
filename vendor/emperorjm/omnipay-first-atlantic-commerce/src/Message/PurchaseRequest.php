<?php

namespace Omnipay\FirstAtlanticCommerce\Message;

/**
 * FACPG2 Purchase Request
 *
 * Required Parameters:
 * amount - Float ex. "10.00",
 * currency - Currency code ex. "USD",
 * card - Instantiation of Omnipay\FirstAtlanticCommerce\CreditCard
 *
 * There are also 2 optional boolean parameters outside of the normal Omnipay parameters:
 * requireAVSCheck - will tell FAC that we want the to verify the address through AVS
 * createCard - will tell FAC to create a tokenized card in their system while it is authorizing the transaction
 */

class PurchaseRequest extends AuthorizeRequest
{
    /**
     * Transaction code (flag as a single pass transaction – authorization and capture as a single transaction)
     *
     * @var int;
     */
    protected $transactionCode = 8;
}
