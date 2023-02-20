<?php
// header("content-type:application/json");
// $draft = "set";
include_once "../includes/function.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // print_r($store_code);
    $myfile = fopen("get_order.json", "w") or die("Unable to open file!");

    $json = file_get_contents('php://input');

    // fwrite($myfile, $json);
    $data1 = json_decode($json, JSON_PRETTY_PRINT);
    // print_r($data1);

    $order_id = $data1['data']['id'];
    $store_hash = str_replace('stores/', '', $data1['producer']);
    $data = gettoken_byhash($store_hash);
    $access_token = $data['token'];
    $hash = $data['hash'];
    $store_name = $data['store_name'];

    $order_details = api_call($access_token, $hash, "/v2/orders/$order_id", array(), 'GET');
    $order_details = json_decode($order_details['response']);

    fwrite($myfile, json_encode($order_details));
    fclose($myfile);




    // $txt = "Jane Doe\n";

    // fwrite($myfile, $txt);

    fclose($myfile);
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "url to send data",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST"
    ));

    $token_response = curl_exec($curl);
    $token_response = json_decode($token_response);

    print_r($token_response);

    $err = curl_error($curl);

    curl_close($curl);
}
