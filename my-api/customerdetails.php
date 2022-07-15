<?php
include_once "../includes/function.php";
$data_obj = json_decode(file_get_contents("php://input", true));
$id = $data_obj->val;
// echo $id;
$shop = $data_obj->shop;

$details = gettoken($shop);
if ($id != "not logged in") {
    // echo $shop;
    $access_token = $details['token'];
    $hash = $details['hash'];
    $login = api_call($access_token, $hash, "/v2/customers/$id", array(), 'GET');
    $user = json_decode($login['response'], true);
    $user_addr = api_call($access_token, $hash, "/v2/customers/$id/addresses", array(), 'GET');
    $addr = json_decode($user_addr['response'], true);
    // $name = $user['first_name'] . $user['last_name'];
    $obj = new stdClass;
    @$obj->success->error = "false";
    @$obj->success->status = "true";
    @$obj->success->id = $id;
    @$obj->data->id = $id;
    $obj->data->email = $user['email'];
    $obj->data->prefix = "";
    $obj->data->suffix = "";
    $obj->data->dob = "";
    $obj->data->firstname = $user['first_name'];
    $obj->data->middlename = "";
    $obj->data->lastname = $user['last_name'];
    $obj->data->company = $user['company'];

    // addresses
    $obj->data->street = $addr[0]['street_1'];
    $obj->data->city = $addr[0]['city'];
    $obj->data->region = $addr[0]['city'];
    $obj->data->country = $addr[0]['country'];
    $obj->data->postcode = $addr[0]['zip'];
    $obj->data->telephone = $addr[0]['phone'];
    $obj->data->vat = "";
    $obj->data->profile_image = "";
    $obj->data->corporate_logo = "";
    echo json_encode($obj, JSON_PRETTY_PRINT);
} else {
    $error = new stdClass;
    @$error->data->success = "false";
    @$error->error->message = "user not logged in";
    echo json_encode($error, JSON_PRETTY_PRINT);
}
