<?php
include_once '../includes/function.php';
$shop = $_REQUEST['store'];
$data = $_REQUEST['params'];
//$qty = $_REQUEST['qty'];
// print_r($data);
// echo gettype($data);
$attr  = json_decode($data, JSON_PRETTY_PRINT);
// print_r($attr);
$pr_id = $attr['prod_id'];
// $color = $attr['super']
$values = array_values($attr['super_attribute']);
// print_r($values[0]);
// $id1 = $attr['color_id'];
// $id2 = $attr['size_id'];
// print_r($attr['color_id']);
// echo $attr['super_attribute'][$id1];
$size = sizeof($attr['super_attribute']);

$data = gettoken($shop);
if ($data) {
    $access_token = $data['token'];
    $hash = $data['hash'];

    $products_list = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/variants", array(), 'GET');
    $products_list = json_decode($products_list['response'], true);
    // echo $products_list['data'][0]['calculated_price'];
    // print_r($products_list);
    if ($size == 0) {
        $price = $products_list['data'][0]['calculated_price'];
    } else if ($size == 1) {
        $id = $values[0];
        foreach ($products_list['data'] as $variant) {
            if ($variant['option_values'][0]['id'] == $id) {
                $price = $variant['calculated_price'];
                break;
            }
        }
    } else if ($size == 2) {
        $id1 = $values[0];
        $id2 = $values[1];
        foreach ($products_list['data'] as $variant) {
            if ($variant['option_values'][0]['id'] == $id1 || $variant['option_values'][1]['id'] == $id1) {
                if ($variant['option_values'][0]['id'] == $id2 || $variant['option_values'][1]['id'] == $id2) {
                    $price = $variant['calculated_price'];
                    break;
                }
            }
        }
    }



    $api = new stdClass;
    @$api->data->price = $price;
    //@$api->data->quantity = $qty;
    echo json_encode($api, JSON_PRETTY_PRINT);
} else {
    echo "store not exists";
}
// include_once '../includes/dbconnect.php';

// echo json_decode($products_list);
