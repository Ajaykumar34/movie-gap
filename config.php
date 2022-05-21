<?php
define('DB_SERVER','');
define('DB_USERNAME','root');
define('DB_PASSWOrd','');
define('DB_NAME','login');
$conn = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWOrd,DB_NAME);
if($conn == false){
    dir('ERROR: Cannot Connect');
}
?>