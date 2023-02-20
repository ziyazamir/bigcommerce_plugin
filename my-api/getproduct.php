<?php
include_once '../includes/function.php';

// include_once '../includes/dbconnect.php';

$pr_id = $id = $_GET['id'];

$store = $_GET['store'];

$data = gettoken($store);

// print_r($data);

if ($data) {

  $access_token = $data['token'];

  $hash = $data['hash'];

  $products_list = api_call($access_token, $hash, "/v3/catalog/products/$pr_id", array(), 'GET');

  $products_list = json_decode($products_list['response'], JSON_PRETTY_PRINT);

  // echo $products_list;

  print_r($products_list);

  // echo gettype($products_list['status']);

  if ($products_list['status'] == 404) {

    $error = new stdClass;

    @$error->data->success = "false";

    @$error->error->message = "product not exists.";

    return json_encode($error, JSON_PRETTY_PRINT);
  } else {

    // getting config options

    $config = $config = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/options", array(), 'GET');

    $config = json_decode($config['response']);

    print_r($config);


    // getting variants of product

    $variants = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/variants", array(), 'GET');

    $variants = json_decode($variants['response'], JSON_PRETTY_PRINT);

    print_r($variants['data']);



    // getting product image

    $images = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/images", array(), 'GET');

    $images = json_decode($images['response']);
    print_r($images);
  }
} else {

  echo "store not exists";
}
