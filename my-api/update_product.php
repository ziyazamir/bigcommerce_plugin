<?php
$index = "index";

include_once "../includes/function.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $json = file_get_contents('php://input');

    $myfile = fopen("updated_pr.json", "w") or die("Unable to open file!");

    fwrite($myfile, $json);
    fclose($myfile);

    $data1 = json_decode($json);
    // print_r($data1);
    $pr_id = $data1['data']['id'];  //-----------id of updated product---------
    $store_hash = str_replace('stores/', '', $data1['producer']);
    $data = gettoken_byhash($store_hash);
    print_r($data);
    // echo $data['store_name'];
    // echo $pr_id;
    // echo $store_hash;
    if ($data) {
        $access_token = $data['token'];
        $hash = $data['hash'];
        $store_name = $data['store_name'];
        $products_list = api_call($access_token, $hash, "/v3/catalog/products/$pr_id", array(), 'GET');
        $products_list = json_decode($products_list['response']);
        // echo $products_list;
        print_r($products_list);
        // echo gettype($products_list['status']);
    } else {
        echo "store not exists";
    }
}
