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

        // print_r($images);
        $images = json_decode($images['response'], JSON_PRETTY_PRINT);

        @$item_object->image->url = $images['data'][0]['url_standard'];



        @$api->data->products->page_info->page_size = 20;

        @$api->data->products->page_info->current_page = 1;



        array_push($api->data->products->items, $item_object);

        echo json_encode($api, JSON_PRETTY_PRINT);



        // $modifier1 = '{

        //     "type": "text",

        //     "required": false,

        //     "config": {

        //       "default_value": "designo-price"

        //     },

        //     "display_name": "designo-price"

        //   }';

        // $modi1 = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/modifiers", $modifier1, 'POST');

        // $modi1 = json_decode($modi1['response'], true);

        // print_r($modi1);



        $modifier2 = '{

            "type": "text",

            "required": false,

            "config": {

              "default_value": "designo-image"

            },

            "display_name": "designo-image"

          }';

        $modi2 = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/modifiers", $modifier2, 'POST');

        $modi2 = json_decode($modi2['response'], true);

        //print_r($modi2);



        $modifier3 = '{

            "type": "text",

            "required": false,

            "config": {

              "default_value": "designo-data"

            },

            "display_name": "designo-data"

          }';

        $modi3 = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/modifiers", $modifier3, 'POST');

        $modi3 = json_decode($modi3['response'], true);

        //print_r($modi3);

        // $modifier4 = '{

        //     "type": "text",

        //     "required": false,

        //     "config": {

        //       "default_value": "designo-price",

        //     },

        //     "display_name": "designo-price"

        //   }';

    }
} else {

    echo "store not exists";
}
