<?php
$index = "set";
// include "inc/dbconnect.php";
include_once "includes/function.php";
// $curl = curl_init();
function verifySignedRequest($signedRequest)
{
    list($encodedData, $encodedSignature) = explode('.', $signedRequest, 2);

    // decode the data
    $signature = base64_decode($encodedSignature);
    $jsonStr = base64_decode($encodedData);
    $data = json_decode($jsonStr, true);

    // confirm the signature
    // $expectedSignature = hash_hmac('sha256', $jsonStr, $clientSecret(), $raw = false);
    // if (!hash_equals($expectedSignature, $signature)) {
    //     error_log('Bad signed request from BigCommerce!');
    //     return null;
    // }
    // print_r($data);
    return $data;
}
$payload = $_GET['signed_payload'];
$obj = verifySignedRequest($payload);
print_r($obj);
