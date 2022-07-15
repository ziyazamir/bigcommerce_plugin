<?php
include_once "../includes/function.php";
// echo "heelo";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $product_id = $data['pr_id'];
    $add_price = $data['price'];
    $shop = $data['shop'];
    $old_cart = $data['cartId'];
    $data1 = gettoken($shop);
    $access_token = $data1['token'];
    $hash = $data1['hash'];


    $quantity = $data['quantity'];
    //price issue fix
    $add_price = $data['price'] / $data['quantity'];
    $object = new stdClass;
    $object->line_items = array();
    $item = new stdClass;
    $item->quantity = $quantity;
    $item->product_id = $product_id;
    $item->list_price = $add_price;
    $item->option_selections = array();
    foreach ($data['options'] as $key => $value) {
        $options = new stdClass;
        $options->option_id = $key;
        $options->option_value = $value;
        array_push($item->option_selections, $options);
    }
    array_push($object->line_items, $item);
    $send = json_encode($object);
    // print_r($send);
    // echo json_encode($object, JSON_PRETTY_PRINT);
    // echo "cart id" . $data['cartId'];
    if (empty($old_cart) || !isset($old_cart)) {
        $add = api_call($access_token, $hash, "/v3/carts?include=redirect_urls", $send, 'POST');
        $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $add['response'], 2);

        // Convert headers into an array
        $headers = array();
        $header_data = explode("\n", $response[0]);
        $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
        array_shift($header_data); // Remove status, we've already set it above
        foreach ($header_data as $part) {
            $h = explode(":", $part);
            $headers[trim($h[0])] = trim($h[1]);
        }

        // Return headers and Shopify's response
        $myarr =  array('headers' => $headers, 'response' => $response[1]);
        $temp_cart =  $myarr['response'];
        // echo $add['response'];
        $new_obj = json_decode($temp_cart, true);
        // // echo $add;
        $new_cart = $new_obj['data']['redirect_urls']['cart_url'];
        echo json_encode($new_cart);
        // // echo $new_cart;
        // $cart_url = api_call($access_token, $hash, "/v3/carts/$new_cart/redirect_urls", array(), 'POST');
        // $cart_url = json_decode($cart_url['response'], true);
        // $redirect_url = $cart_url['data']['cart_url'];
        // echo json_encode($redirect_url);
    } else {
        // cart is here 
        $add = api_call($access_token, $hash, "/v3/carts/$old_cart/items", $send, 'POST');
        // print_r($add);
        $add = json_decode($add['response'], true);
        $redirect_url = json_encode("/cart.php");
        echo $redirect_url;
    }
}
