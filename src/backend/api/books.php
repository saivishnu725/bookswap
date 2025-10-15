<?php

session_start();

$host = "localhost";
$user = "root";
$password = "";
$dbname = "bookswap";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed");
} else {
    echo "Connected successfully";
}

?>