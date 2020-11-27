When adding a new payment processor you need to create a new
.mgd.php file for it in the Metadata file.

In addition to the payment processor database fields
(currently documented in those files) there are some
metadata fields you can add that help describe
the payment processor to the code.

- pass_through_fields - these fields are passed to the gate way.
For example SagePay has
     ```'pass_through_fields' => ['billingForShipping' => 1],```

     This means the parameter billingForShipping is added to the array
     of values passed to the purchase or other payment action during processing.
