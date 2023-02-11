<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "caesuralitmag_data"; 

$conn = mysqli_connect($host, $user, $password, $db);

if(!$conn) {
    die("Could not connect to database");
}

?>