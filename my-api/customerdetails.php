<?php
include_once "../includes/function.php";

$data_obj = json_decode(file_get_contents("php://input", true));
$id = $_GET['id'];
// echo $id;
$shop = $_GET['shop'];

$details = gettoken($shop);
// echo $shop;
$access_token = $details['token'];
$hash = $details['hash'];
$login = api_call($access_token, $hash, "/v2/customers/$id", array(), 'GET');
$user = json_decode($login['response'], true);
print_r($user);
$user_addr = api_call($access_token, $hash, "/v2/customers/$id/addresses", array(), 'GET');
$addr = json_decode($user_addr['response'], true);
print_r($addr);
    // $name = $user['first_name'] . $user['last_name'];
