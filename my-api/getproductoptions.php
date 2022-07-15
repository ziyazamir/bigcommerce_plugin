<?php
include_once '../includes/function.php';
$reque = $_REQUEST;
$myfile = fopen("price.json", "w") or die("Unable to open file!");

fwrite($myfile, json_encode($reque));

fclose($myfile);
$shop = $_REQUEST['store'];
// $data = $_REQUEST['params'];
$attr  = json_decode($data, JSON_PRETTY_PRINT);
$pr_id = $_REQUEST['id'];
// $index = "set";
$data = gettoken($shop);
$access_token = $data['token'];
$hash = $data['hash'];
$pr_options = api_call($access_token, $hash, "/v3/catalog/products/$pr_id/modifiers", array(), 'GET');
$myfile = fopen("options.json", "w") or die("Unable to open file!");

fwrite($myfile, json_encode($pr_options));

fclose($myfile);
$pr_options = json_decode($pr_options['response'], true);
// print_r($pr_options);
$pr_options = $pr_options['data'];
$options = new stdClass;
@$options->data->dnbProductOptions->options = array();

foreach ($pr_options as $option) {
    if ($option['type'] == "text" || $option['type'] == "checkbox" || $option['type'] == "radio_buttons" || $option['type'] == "dropdown") {
        $single = new stdClass;
        $single->id = $option['id'];
        if ($option['display_name'] == "designo-price" || $option['display_name'] == "designo-data" || $option['display_name'] == "designo-image") {
            $type = "hidden";
            $single->name = $option['display_name'] . "[" . $option['id'] . "]";
        } else {
            $type = $option['type'];
            $single->name = $option['id'];
        }
        if ($option['type'] == 'radio_buttons' || $option['type'] == 'checkbox') {
            $type = "radio";
        }
        // if ($option['type'] == 'radio_buttons') {
        //     $type = "radio";
        // }
        $single->type = $type;
        if($option['config']['checkbox_label']){
            $single->title = $option['display_name'] . "<br>" . $option['config']['checkbox_label'];
            $single->label = $option['display_name'] . "<br>" . $option['config']['checkbox_label'];
        }else{
            $single->title = $option['display_name'];
            $single->label = $option['display_name'];
        }
        $single->is_require = $option['required'];
        $single->sort_order = $option['sort_order'];
        if ($option['type'] == 'checkbox') {
            $single->values = array();
            foreach ($option['option_values'] as $value) {
                $val = new stdClass;
                $val->option_type_id = $value['id'];
                $val->option_id = $value['id'];
                $val->default_title = $value['label'];
                // $val->default = $value['is_default'];
                array_push($single->values, $val);
            }
        }
        if ($option['type'] == 'dropdown' || $option['type'] == 'radio_buttons') {
            $single->values = array();
            foreach ($option['option_values'] as $value) {
                $val = new stdClass;
                $val->option_type_id = $value['id'];
                $val->option_id = $value['id'];
                $val->default_title = $value['label'];
                $val->default = $value['is_default'];
                array_push($single->values, $val);
            }
        }
        //  print_r($values);
        array_push($options->data->dnbProductOptions->options, $single);
        // array_push($options->data->dnbProductOptions->options, json_decode($temp));
    }
}
$qtyData = array(
    "id" => 'quantityBox',
    "type" => 'text',
    "title" => 'Quantity',
    "label" => 'QuantityBox',
    "is_require" => '',
    "sort_order" => '',
    "value" => 1,
);
array_push($options->data->dnbProductOptions->options, $qtyData);

$myfile = fopen("sending_options.json", "w") or die("Unable to open file!");


fwrite($myfile, json_encode($options));

fclose($myfile);

echo json_encode($options, JSON_PRETTY_PRINT);

exit;
