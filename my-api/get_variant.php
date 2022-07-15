<?php
include_once "../includes/function.php";
$data1 = json_decode(file_get_contents("php://input", true));


$variants = api_call($access_token, $hash, "/v3/catalog/products/$id/variants", array(), 'GET');
$variants = json_decode($variants['response'], JSON_PRETTY_PRINT);
// print_r($variants['data']);
$var = $variants['data'];
