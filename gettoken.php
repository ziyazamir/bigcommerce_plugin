<?php

include_once "includes/dbconnect.php";

include_once "includes/function.php";

$client_id = 'YOUR-CLIENT-ID';

$client_secret = 'YOUR-CLIENT-SECRET';

$redirect_uri = 'https://' . $app_domain . '/gettoken.php';  //app_domain is present in inlcudes/function.php

$postfields = array(

    "client_id" => $client_id,

    "client_secret" => $client_secret,

    "redirect_uri" => $redirect_uri,

    "grant_type" => "authorization_code",

    "code" => $_GET['code'],

    "scope" => $_GET['scope'],

    "context" => $_GET['context'],

);

// print_r($postfields);

$postfields = http_build_query($postfields);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://login.bigcommerce.com/oauth2/token');


curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

curl_setopt($ch, CURLOPT_VERBOSE, 0);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);



$response = curl_exec($ch);

//print_r($response);

$error_msg = curl_error($ch);

$result = json_decode($response, JSON_PRETTY_PRINT);

//echo " result:" . $result;


$storeHash = str_replace('stores/', '', $_GET['context']);

$access_token = $result['access_token'];

//echo $result['access_token'];

$myfile = fopen("token.json", "w") or die("Unable to open file!");



fwrite($myfile, json_encode($result));

fclose($myfile);



// getting store details



$store = api_call($access_token, $storeHash, "/v2/store", array(), 'GET');

$store = json_decode($store['response'], true);

// $store  = json_decode($store);



$store = $store['domain'];

echo $store;



// adding script-tag to the store

$data = '{

    "name": "App-script",

    "description": "Add Customize Functionalities",

    "src": "https://' . $app_domain . '/includes/script.js",

    "auto_uninstall": true,

    "load_method": "default",

    "location": "footer",

    "visibility": "all_pages",

    "kind": "src",

    "consent_category": "essential"

  }';

$script_tag = api_call($access_token, $storeHash, "/v3/content/scripts", $data, 'POST');

$script_tag = json_decode($script_tag['response'], true);

//print_r($script_tag);

// echo $script_tag;



//--------------creating hook for updated product------------

$hook = '{

    "headers": {},

    "scope": "store/product/updated",

    "destination": "https://' . $app_domain . '/my-api/update_product.php",

    "is_active": true

  }';



$pr_hook = api_call($access_token, $storeHash, "/v3/hooks", $hook, 'POST');

$pr_hook = json_decode($pr_hook['response'], true);

//print_r($pr_hook);



//------------------ order webhook--------------



$orderhook = '{

    "headers": {},

    "scope": "store/order/created",

    "destination": "https://' . $app_domain . '/my-api/add_order.php",

    "is_active": true

  }';

$order_hook = api_call($access_token, $storeHash, "/v3/hooks", $orderhook, 'POST');

$order_hook = json_decode($order_hook['response'], true);

//--------------order update webhook----------------------

$orderupdatehook = '{

    "headers": {},

    "scope": "store/order/updated",

    "destination": "https://' . $app_domain . '/my-api/update_order.php",

    "is_active": true

  }';

$update_order_hook = api_call($access_token, $storeHash, "/v3/hooks", $orderupdatehook, 'POST');

$update_order_hook = json_decode($update_order_hook['response'], true);

//print_r($update_order_hook);

// creating a page through api
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.bigcommerce.com/stores/$storeHash/v2/pages",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\"name\":\"my_page\",\"body\":\"<!DOCTYPE html><html lang=en><head><meta charset=UTF-8><meta http-equiv=X-UA-Compatible content=IE=edge><meta name='viewport' content='user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi'><title>App Page</title></head><body style='margin:0'></body><script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script><script src='https://" . $app_domain . "/includes/script.js' ></script></html>\",\"is_visible\":false,\"parent_id\":0,\"sort_order\":0,\"type\":\"raw\",\"is_homepage\":false,\"is_customers_only\":false,\"search_keywords\":\"\",\"has_mobile_version\":true,\"mobile_body\":\"\",\"content_type\":\"text/html\"}",
    CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "content-type: application/json",
        "x-auth-token: $access_token"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}




$query = "SELECT COUNT(*) FROM main WHERE store='$store'";

$stmt = $pdo->query($query);

$n = $stmt->fetchColumn();

// echo $n;

if ($n > 0) {

    $update = "UPDATE main SET `store_hash`='$storeHash', `token`= '$access_token' ,`install_date`=NOW() WHERE store='$store'";

    $stmt = $pdo->prepare($update);

    $val = $stmt->execute();
    print_r($val);
    if ($val = $stmt->execute()) {

        echo "<script> alert('updated succesfully');</script>";
        // header("Refresh:0");
        // header("location:index.php");
        header("location:https://store-" . $storeHash . ".mybigcommerce.com/manage/app/37237"); //change it after first app installation
    } else {

        echo "<script> alert('something went wrong in updation');</script>";
    }
} else {

    $query = "INSERT INTO main (store,store_hash,token,install_date) VALUES('$store','$storeHash','$access_token',NOW())";

    $stmt = $pdo->prepare($query);

    // $res = $stmt->execute();

    if ($res = $stmt->execute()) {

        echo "<script> alert('inserted succesfully');</script>";
        // header("Refresh:0");
        // header("location:index.php");
        header("location:https://store-" . $storeHash . ".mybigcommerce.com/manage/app/37237");
        // header("location:https://" . $shop . "/admin/apps");
    } else {

        echo "error installation";
    };
}

// echo "\n";

// echo $storeHash;
