<?php
$index = "index";
// echo "hhelo";
include_once "../includes/function.php";
// echo "heelo";
// $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
// $txt = "John Doe\n";
// fwrite($myfile, $txt);
// $txt = "Jane Doe\n";
// fwrite($myfile, $txt);
// fclose($myfile);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $store_code = $_SERVER;
    // print_r($store_code);
    $json = file_get_contents('php://input');
    $myfile = fopen("updated_pr.json", "w") or die("Unable to open file!");

    fwrite($myfile, $json);

    fclose($myfile);
    // echo $json;
    // $data = json_decode($json, JSON_PRETTY_PRINT);
    // $pr_id = $data['data']['id'];
    // echo $pr_id;
    $data1 = json_decode($json, JSON_PRETTY_PRINT);
    // print_r($data1);
    $pr_id = $data1['data']['id'];
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
        $designo_url = geturl($store_name);
        $products_list = api_call($access_token, $hash, "/v3/catalog/products/$pr_id", array(), 'GET');
        $products_list = json_decode($products_list['response'], JSON_PRETTY_PRINT);
        // echo $products_list;
        // print_r($products_list);
        // echo gettype($products_list['status']);
        if ($products_list['status'] == 404) {
            $error = new stdClass;
            @$error->data->success = "false";
            @$error->error->message = "product not exists.";
            echo json_encode($error, JSON_PRETTY_PRINT);
        } else {
            $pr = $products_list['data'];
            // echo json_decode($products_list);
            $api = new stdClass;
            @$api->data->products->total_count = "1";
            @$api->data->products->items = array();

            $item_object = new stdClass;
            $item_object->name = $pr['name'];
            $item_object->sku = $pr['id'];
            $item_object->categories = array();
            $item_object->color = "NULL";
            $item_object->size = "NULL";
            $item_object->configurable_options = array();
            $item_object->variants = array();

            // getting config options
            $config = $config = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/options", array(), 'GET');
            $config = json_decode($config['response'], JSON_PRETTY_PRINT);
            foreach ($config['data'] as $data) {
                $obj = new stdClass;
                $obj->attribute_id = $data['id'];
                $obj->attribute_code = $data['display_name'];
                array_push($item_object->configurable_options, $obj);
            }

            // getting variants of product
            $variants = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/variants", array(), 'GET');
            $variants = json_decode($variants['response'], JSON_PRETTY_PRINT);
            // print_r($variants['data']);
            $var = $variants['data'];
            foreach ($var as $single_var) {
                $id = $single_var['id'];
                $var_obj = new stdClass;
                $var_obj->attributes = array();
                foreach ($single_var['option_values'] as $option) {

                    $attr_obj = new stdClass;
                    $attr_obj->label = $option['label'];
                    $attr_obj->code = $option['option_display_name'];
                    $attr_obj->value_index = $option['id'];
                    array_push($var_obj->attributes, $attr_obj);
                }
                array_push($item_object->variants, $var_obj);
            }

            @$item_object->short_description->html = $pr['description'];

            // getting product image
            $images = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/images", array(), 'GET');
            $images = json_decode($images['response'], JSON_PRETTY_PRINT);
            @$item_object->image->url = $images['data'][0]['url_standard'];

            @$api->data->products->page_info->page_size = 20;
            @$api->data->products->page_info->current_page = 1;
            // print_r($api);
            array_push($api->data->products->items, $item_object);
            // echo json_encode($api, JSON_PRETTY_PRINT);
            print_r($api);
            $myfile = fopen("sentforupdate.json", "w") or die("Unable to open file!");
            fwrite($myfile, json_encode($api));
            fclose($myfile);
            // $token =  $token_response->token;
            $post_data = json_encode($api, true);
            //  print_r($post_data);
            //    echo"<pre>"; print_r(json_decode($ans,true)['data']); exit;
            // Prepare new cURL resource
            $urll = $designo_url . 'api/update-product';
            $url = str_replace(" ", '%20', $urll);
            $post = [
                'store_code' => $store_name,
                'SKU' => $pr_id,
                'params' => json_encode($api, true)
            ];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // curl_setopt(
            //     $ch,
            //     CURLOPT_HTTPHEADER,
            //     array(
            //         'Content-Type: application/json',
            //         'Authorization:' . $token
            //     )
            // );
            $result = curl_exec($ch);
            echo $result;
            $myfile = fopen("pr_result.json", "w") or die("Unable to open file!");
            fwrite($myfile, json_encode($result));
            // $txt = "Jane Doe\n";
            // fwrite($myfile, $txt);
            fclose($myfile);
            curl_close($ch);
            var_dump($result);
            exit;
            // handle curl error
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
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
            curl_close($ch);
        }
    } else {
        echo "store not exists";
    }
}
