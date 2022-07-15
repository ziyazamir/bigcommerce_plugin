<?php
include_once "../includes/function.php";
$data1 = json_decode(file_get_contents("php://input", true));
$id = $data1->val;
// echo $id;
$shop = $data1->shop;

$details = gettoken($shop);
if ($details) {
    $access_token = $details['token'];
    $hash = $details['hash'];
    $login = api_call($access_token, $hash, "/v2/customers/$id", array(), 'GET');
    $login = json_decode($login['response'], true);
    // print_r($login);
    $name = $login['first_name'] . " " .  $login['last_name'];
    $email = $login['email'];
    $obj = new stdClass;
    @$obj->data->success = "true";
    @$obj->data->data->id = $id;
    $obj->data->data->user_name = $name;
    $obj->data->data->email = $email;
    // $obj->data->data->form_key = "";
    echo json_encode($obj, JSON_PRETTY_PRINT);
    // $api = new stdClass;
    // @$api->data->price = $login['data']['price'];
    //@$api->data->quantity = $qty;
    // echo json_encode($api, JSON_PRETTY_PRINT);
} else {
    echo "store not exists";
}
