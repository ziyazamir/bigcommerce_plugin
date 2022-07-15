<?php
include_once "dbconnect.php";
$designo_url = "shopify.designo.software";
$app_domain = "bigcommerceapp.designo.software/v1_ppk";
if (!isset($index)) {
    header('Access-Control-Allow-Origin: *');

    header('Access-Control-Allow-Headers: *');

    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');



    if (isset($draft)) {
        header('Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with');
    } else {
        header('Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with');
        header('Content-Type: application/json');
    }
}


header("Content-Security-Policy: connect-src 'self';");
// https://api.bigcommerce.com/stores/pxint2anco/v3/catalog/products/111
// https://api.bigcommerce.com/stores/pxint2anco/v3/catalog/products/111
function gettoken($store)
{
    global $pdo;
    $sql = "SELECT * FROM main WHERE store='$store'";
    // echo $sql;
    $stmt = $pdo->query($sql);
    if ($value = $stmt->fetch()) {
        // return $value['token'], $value['store_hash']; 
        return array('hash' => $value['store_hash'], 'token' => $value['token']);
    } else {
        return false;
    }
}
function geturl($store)
{
    global $pdo;
    $sql = "SELECT * FROM users WHERE store='$store'";
    // echo $sql;
    $stmt = $pdo->query($sql);
    if ($value = $stmt->fetch()) {
        // return $value['token'], $value['store_hash']; 
        return $value['link'];
    } else {
        return false;
    }
}
function gettoken_byhash($hash)
{
    global $pdo;
    $sql = "SELECT * FROM main WHERE store_hash='$hash'";
    // echo $sql;
    $stmt = $pdo->query($sql);
    if ($value = $stmt->fetch()) {
        // return $value['token'], $value['store_hash']; 
        return array('store_name' => $value['store'], 'hash' => $value['store_hash'], 'token' => $value['token']);
    } else {
        return false;
    }
}

function insert_users($selected, $url, $shop, $hash)
{
    global $pdo;
    $query = "INSERT INTO users (selected,link,store,store_hash) VALUES('$selected','$url','$shop','$hash')";
    echo $query;
    $stmt = $pdo->prepare($query);
    $res = $stmt->execute();
    if ($res) {

        echo "<script> alert('added succesfully');</script>";
    } else {
        echo "<script> alert('something went wrong');</script>";
        // echo PDOException;
    }
    header("Refresh:0");
}




function api_call($token, $hash, $api_endpoint, $query = array(), $method = 'GET', $request_headers = array())
{

    // Build URL
    $url = "https://api.bigcommerce.com/stores/" . $hash . $api_endpoint;
    // if (!is_null($query) && in_array($method, array('GET',     'DELETE'))) $url = $url . "?" . http_build_query($query);
    // echo $url;
    // print_r($query);
    // Configure cURL
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, TRUE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
    // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
    curl_setopt($curl, CURLOPT_USERAGENT, 'My New Designo App v.1');
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

    // Setup headers
    $request_headers[] = "accept: application/json";
    $request_headers[] = "content-type: application/json";
    if (!is_null($token)) $request_headers[] = "x-auth-token: " . $token;
    curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

    if ($method != 'GET' && in_array($method, array('POST', 'PUT'))) {
        if (is_array($query)) $query = http_build_query($query);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
    }

    // Send request to Shopify and capture any errors
    $response = curl_exec($curl);
    $error_number = curl_errno($curl);
    $error_message = curl_error($curl);

    // Close cURL to be nice
    curl_close($curl);

    // Return an error is cURL has a problem
    if ($error_number) {
        return $error_message;
    } else {

        // No error, return Shopify's response by parsing out the body and the headers
        $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

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
        return array('headers' => $headers, 'response' => $response[1]);
        // return $response;
    }
}
