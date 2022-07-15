<?php

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.bigcommerce.com/stores/pxint2anco/v3/hooks",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\"headers\":{},\"scope\":\"store/product/updated\",\"destination\":\"https://cf7d-2405-201-4017-857-5d29-5f33-8c2e-ec2a.ngrok.io/testings/big_pr_webhook.php\",\"is_active\":true}",
    CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "content-type: application/json",
        "x-auth-token: llw9efk2utxz3uo2j9zmcp721v87tal"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}
