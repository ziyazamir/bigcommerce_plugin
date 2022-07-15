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
    $designo_url = geturl($store_name);
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

    $obj->order_array = array();



    $order = new stdClass;

    @$order->customer_details->name = $shipping_address[0]->first_name . ' ' . $shipping_address[0]->last_name;

    $order->customer_details->email = $order_details->billing_address->email;
    if ($order_details->billing_address->phone == null) {
        $phone = "123456789";
    } else {
        $phone = $order_details->billing_address->phone;
    }
    $order->customer_details->phone = $phone;



    @$order->address->shipping_address = $shipping_address[0]->street_1 . " " . $shipping_address[0]->street_2;

    $order->address->shipping_country = $shipping_address[0]->country;

    $order->address->shipping_state = $shipping_address[0]->state;

    $order->address->shipping_city = $shipping_address[0]->city;

    $order->address->shipping_zip = $shipping_address[0]->zip;

    $order->address->shipping_contact = $shipping_address[0]->phone;



    $order->address->billing_address = $order_details->billing_address->street_1 . "" . $order_details->billing_address->street_2;

    $order->address->billing_country = $order_details->billing_address->country;

    $order->address->billing_state = $order_details->billing_address->state;

    $order->address->billing_city = $order_details->billing_address->city;

    $order->address->billing_zip = $order_details->billing_address->zip;

    $order->address->billing_contact = $order_details->billing_address->phone;



    @$order->order_details->order_id = $order_id;

    $order->order_details->order_status = $order_details->status;

    $order->order_details->order_date = $order_details->date_created;

    $order->order_details->store_name = $store_name;
    $order->order_details->store_code = $store_name;

    // print_r($data->payment_gateway_names);
    if (empty($order_details->payment_method)) {
        $p_m = "cash";
    } else {
        $p_m = $order_details->payment_method;
    }
    if (empty($order_details->payment_status)) {
        $p_s = "not define";
    } else {
        $p_s = $order_details->payment_method;
    }

    $order->order_details->payment_mode = $p_m;

    $order->order_details->payment_status = $p_s;

    $order->order_details->subtotal = $order_details->subtotal_inc_tax;

    $order->order_details->shipping_amount = $order_details->shipping_cost_tax;

    $order->order_details->discount_amount = $order_details->discount_amount;

    $order->order_details->grand_total = $order_details->total_inc_tax;

    $order->order_details->status = "1";





    $order->order_items = array();

    foreach ($products as $line_items) {

        foreach ($line_items->product_options as $option) {


            if ($option->display_name == "designo-data") {
                $byrequest = $option->display_value;
            } else if ($option->display_name == "designo-image") {
                $thumb_image = $option->display_value;
            } else if ($option->display_name == "designo-price") {
                $custom_price = $option->display_value;
            }

            if ($option->display_name == "designo-data" && $option->display_value != "designo-data") {
                // echo $items->name;
                // if($option->display_name == "")
                $items = new stdClass;
                // $str1 = substr($line_items->name, 11);
                $items->name = $line_items->name;

                $items->thumb_image = $thumb_image;
                $items->info_buyRequest = $byrequest;
                $items->SKU = $line_items->product_id;

                $items->qty = $line_items->quantity;

                $items->price = $pr_price = $line_items->price_inc_tax;

                $items->subtotal = $line_items->total_inc_tax;

                $items->tax = $line_items->total_tax;

                $items->tax_amount = "";

                $items->discount = "";

                $items->total_amount = $line_items->total_inc_tax;
                $check = true;
            }
        }
        if ($check) {
            array_push($order->order_items, $items);
        }
        $check = false;
    }
    array_push($obj->order_array, $order);

    $response = json_encode($obj, JSON_PRETTY_PRINT);
    $myfile = fopen("sending_order.json", "w") or die("Unable to open file!");

    fwrite($myfile, $response);

    // $txt = "Jane Doe\n";

    // fwrite($myfile, $txt);

    fclose($myfile);
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $designo_url . "api/studio/ecomm-token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST"
        // CURLOPT_HTTPHEADER => array(
        //     "cache-control: no-cache",
        //     "postman-token: 9f12df12-f4cc-6b03-d03c-fa4a236f3acb"
        // ),
    ));

    $token_response = curl_exec($curl);
    $token_response = json_decode($token_response);

    print_r($token_response);

    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $token =  $token_response->token;
        $post_data = json_encode($rawdata, true);
        //  print_r($post_data);

        // Prepare new cURL resource
        $crl = curl_init($designo_url . 'api/studio/add-order');
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLINFO_HEADER_OUT, true);
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $response);

        // Set HTTP Header for POST request 
        curl_setopt(
            $crl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                // 'Content-Length: ' . strlen($payload),
                'Authorization:' . $token
            )
        );

        // Submit the POST request
        $result = curl_exec($crl);
        echo $result;
        $myfile = fopen("order_result.json", "w") or die("Unable to open file!");

        fwrite($myfile, $result);

        // $txt = "Jane Doe\n";

        // fwrite($myfile, $txt);

        fclose($myfile);

        // handle curl error
        if (curl_errno($crl)) {
            $error_msg = curl_error($crl);
        }

        if ($result === false) {
            // throw new Exception('Curl error: ' . curl_error($crl));
            //print_r('Curl error: ' . curl_error($crl));
            $result_noti = 0;
            die();
        } else {

            $result_noti = 1;
            die();
        }
        // Close cURL session handle
        curl_close($crl);
    }
}
