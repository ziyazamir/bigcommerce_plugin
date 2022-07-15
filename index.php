<?php
$index = "set";
// include "inc/dbconnect.php";
include_once "includes/function.php";
// $curl = curl_init();
function verifySignedRequest($signedRequest)
{
    list($encodedData, $encodedSignature) = explode('.', $signedRequest, 2);

    // decode the data
    $signature = base64_decode($encodedSignature);
    $jsonStr = base64_decode($encodedData);
    $data = json_decode($jsonStr, true);


    return $data;
}
$payload = $_GET['signed_payload'];
$obj = verifySignedRequest($payload);
$hash =  $obj['store_hash'];
$val = gettoken_byhash($hash);
$shop =  $val['store_name'];

$query = "SELECT COUNT(*) FROM users WHERE store='$shop'";
$stmt = $pdo->query($query);
$n = $stmt->fetchColumn();

if ($n == 1) {
    global $pdo;
    $query = "SELECT * FROM users WHERE store='$shop'";
    $stmt = $pdo->query($query);
    $val = $stmt->fetch();
    // $n = 1;
    // echo "<script> alert($n);</script>";
    $designo_link = $val['link'];
    // echo $val['selected'];
    if ($val['selected'] == "yes") {
        $yes = "selected";
    } else {
        $no = "selected";
    }
} else {
    // $n = 0;
    echo "<script> alert('store is not present');</script>";
}
// 
?>
<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Document</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script>
        function url_show() {
            $("#url-alert").show();
        }
    </script>


</head>



<body>

    <div class="container-fluid row justify-content-center align-items-center">
        <!--  <div class="col-12">
            <form method="post" style="float: right;">
                <button type="submit" name="install_theme" class="btn btn-primary">Integration</button>
            </form>
        </div>
 -->
        <div class="col-6">
            <div class="col-md-12 text-center mb-5 mt-5"><img src="https://alltechstrends.com/bigcommerce/includes/logo-design.JPG" style="max-width:100%;" /></div>
            <form method="POST">
                <!-- <div class="mb-3">

                    <label class="form-label">Store Domain</label>

                    <input name="link" type="url" class="form-control" value="<?php echo $designo_link ?>" required>

                </div> -->
                <div class="mb-3">

                    <label class="form-label">Enable DESIGNO</label>

                    <select name="options" class="form-select" required>

                        <option <?php echo $yes ?> value="yes">yes</option>

                        <option <?php echo $no ?> value="no">no</option>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">DESIGNO URL</label>

                    <input name="link" type="url" class="form-control" value="<?php echo $designo_link ?>" required>

                </div>
                <div id="url-alert" class="alert alert-danger" style="display: none;" role="alert">
                    Provided URL is not correct.
                </div>

                <button type="submit" name="submit" class="btn btn-primary">Submit</button>

            </form>

        </div>

    </div>

    <?php
    // echo $n;
    // if (isset($_POST["install_theme"])) {
    //     include_once "inc/theme.php";
    // }

    if (isset($_POST["submit"])) {
        // echo $link;
        $url = $_POST['link'];
        $slash = substr($url, -1);
        if ($slash != "/") {
            $url .= "/";
            // echo "<script> alert('$url');</script>";
        }
        $selected = $_POST["options"];
        if ($selected == "yes") {
            echo "<script> alert('App is Enabled');</script>";
        } else {
            echo "<script> alert('App is Disabled');</script>";
        }
        if (strpos($url, 'designo.software')) {
            if ($n == 1) {

                $update = "UPDATE users SET `selected`= '$selected', `link`='$url' WHERE store='$shop'";
                $stmt = $pdo->prepare($update);
                $val = $stmt->execute();
                if ($val = $stmt->execute()) {
                    // echo "<script> alert('updated succesfully');</script>";
                } else {
                    echo "<script> alert('something went wrong in updation');</script>";
                }
                header("Refresh:0");
            } else if ($n == 0) {
                $query = "INSERT INTO users (selected,link,store,store_hash) VALUES('$selected','$url','$shop','$hash')";
                echo $query;
                $stmt = $pdo->prepare($query);
                $res = $stmt->execute();
                if ($res) {

                    // echo "<script> alert('added succesfully');</script>";
                } else {
                    echo "<script> alert('something went wrong');</script>";
                    // echo PDOException;
                }
                header("Refresh:0");
                // insert_users($selected, $url, $shop, $hash);
            }
        } else {
            // echo "<script> alert('invalid url') </script>";
            echo "<script> url_show() </script>";
        }



        // $shop = $_POST['store_name'];
        // echo "<script> alert('$selected<br>$url');</script>";


    }


    ?>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>



</html>