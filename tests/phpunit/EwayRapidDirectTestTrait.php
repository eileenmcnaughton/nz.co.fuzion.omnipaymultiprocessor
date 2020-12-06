<?php

use GuzzleHttp\Psr7\Response;

/**
 * Class EwayRapidDirectTestTrait
 *
 * This trait defines a number of helper functions for testing EwayRapidDirect.
 */
trait EwayRapidDirectTestTrait {

  protected function addMockTokenResponse() {
    $this->getMockClient()->addResponse(new Response(200, [],
      '{"AuthorisationCode":"460707","ResponseCode":"00","ResponseMessage":"A2000","TransactionID":19340838,"TransactionStatus":true,"TransactionType":"Purchase","BeagleScore":-1,"Verification":{"CVN":0,"Address":0,"Email":0,"Mobile":0,"Phone":0},"Customer":{"CardDetails":{"Number":"411111XXXXXX1111","Name":"r r","ExpiryMonth":"01","ExpiryYear":"21","StartMonth":null,"StartYear":null,"IssueNumber":null},"TokenCustomerID":null,"Reference":"","Title":"Mr.","FirstName":"Samantha","LastName":"Smith","CompanyName":"","JobDescription":"","Street1":"95 Seasame St","Street2":"","City":"Oscarville","State":"BOP","PostalCode":"0185","Country":"nz","Email":"admin@example.com","Phone":"","Mobile":"","Comments":"","Fax":"","Url":""},"Payment":{"TotalAmount":1000,"InvoiceNumber":"","InvoiceDescription":"Help Support CiviCRM!-r-","InvoiceReference":"","CurrencyCode":"AUD"},"Errors":null}'
    ));
  }
}
