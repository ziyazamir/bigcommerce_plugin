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
    $order_id = $data1['data']['id']; //*************id of order created***************
    $store_hash = str_replace('stores/', '', $data1['producer']);
    $data = gettoken_byhash($store_hash);
    $access_token = $data['token'];
    $hash = $data['hash'];
    $store_name = $data['store_name'];


    $order_details = api_call($access_token, $hash, "/v2/orders/$order_id", array(), 'GET');
    $order_details = json_decode($order_details['response']);

    // getting order product 
    $products = api_call($access_token, $hash, "/v2/orders/$order_id/products", array(), 'GET');
    $products = json_decode($products['response']);

    // getting shipping details 
    $shipping_address = api_call($access_token, $hash, "/v2/orders/$order_id/shipping_addresses", array(), 'GET');
    $shipping_address = json_decode($shipping_address['response']);

    fwrite($myfile, $json . json_encode($order_details) . json_encode($$products) . json_encode($shipping_address));
    fclose($myfile);

    // print_r($data);
    $obj =  new stdClass;


    $order->order_items = array();
}
