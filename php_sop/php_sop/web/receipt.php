<?php include 'security.php' ?>

<html>
<head>
    <title>Secure Acceptance - API Payment Form Example</title>
    <link rel="stylesheet" type="text/css" href="payment.css"/>
</head>
<body>

<fieldset id="response">
    <legend>Receipt</legend>
    <div>
        <form id="receipt">
            <?php
                 foreach($_REQUEST as $name => $value) {
                     $params[$name] = $value;
                     echo "<span>" . $name . "</span><input type=\"text\" name=\"" . $name . "\" size=\"50\" value=\"" . $value . "\" readonly=\"true\"/><br/>";
                 }

                 echo "<span>Signature Verified:</span><input type=\"text\" name=\"verified\" size=\"50\" value=\"";
                 if (strcmp($params["signature"], sign($params))==0) {
                     echo "True";
                 } else {
                     echo "False";
                 }
                 echo "\" readonly=\"true\"/><br/>";
            ?>
        </form>
    </div>
</fieldset>

</body>
</html>
