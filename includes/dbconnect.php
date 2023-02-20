<?php

$username = "bigcommerceapp";

$password = "password";

$host = "localhost";

$dbname = "bigcommerceapp";

$dsn = "mysql:host=$host;dbname=$dbname";



$pdo = new PDO($dsn, $username, $password);


// $sql="SELECT * FROM main";
// $row = $pdo->query($sql);
// $publishers = $row->fetchAll(PDO::FETCH_ASSOC);
//                       print_r($publishers);
function test_input($data)

{



    $data = trim($data);



    $data = stripslashes($data);



    $data = htmlspecialchars($data);



    return $data;
}



$meta[1] = "some meta data";



$title[1] = "title";



if ($pdo) {

    // echo "You are connected";

} else {



    echo "Sorry , You are not connected";
}
