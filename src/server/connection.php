<?php

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "bookswap";

$conn = mysqli_connect(
    $DB_HOST,
    $DB_USER,
    $DB_PASS,
    $DB_NAME
)
    or die("Connection failed: " . mysqli_connect_error());

?>