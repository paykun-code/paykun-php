<?php

require '../../src/Payment.php';
require '../../src/Crypto.php';
require '../../src/Validator.php';


$obj = new \Paykun\Checkout\Payment('<merchantId>', '<accessToken>', '<encryptionKey>', true, true);
$response = $obj->getTransactionInfo($_REQUEST['payment-id']);

var_dump($response);
if(is_array($response) && !empty($response)) {

    if($response['status'] && $response['data']['transaction']['status'] == "Success") {
        echo "Transaction success";
    } else {
        echo "Transaction failed";
    }
}

?>