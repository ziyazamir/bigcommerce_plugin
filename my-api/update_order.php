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

    fwrite($myfile, json_encode($order_details));
    fclose($myfile);


    $data = json_decode($data);
    $obj = new stdClass;
    $obj->data->store_code = $store_name;
    $obj->data->order_id = $order_id;
    $obj->data->order_status = $order_details->status;
    // print_r($data);

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
        $crl = curl_init($designo_url . 'api/studio/update-order');
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
