<?php

$username = "bigcommerceapp";



$password = "Rts1wTZFUZsebPs";



$host = "localhost";



$dbname = "bigcommerceapp";





// global $pdo;





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



$meta[1] = "This Website is an ultimate source of codinginfotech and start-ups around the world. It covers all upcoming technology and start-ups in the world.";



$title[1] = "Codinginfotech";



if ($pdo) {



    // echo "You are connected";

} else {



    echo "Sorry , You are not connected";

}

