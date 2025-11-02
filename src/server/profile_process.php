<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include('connection.php');

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->get_result()->fetch_assoc();
if (!$user) {
    header("Location: ../index.php");
    die("User not found");
}
$first_name = htmlspecialchars($user['first_name']);
$last_name = htmlspecialchars($user['last_name']);
$email = htmlspecialchars($user['email']);
$date_of_birth = htmlspecialchars($user['date_of_birth']);
$college_name = htmlspecialchars($user['college_name']);
$phone_primary = htmlspecialchars($user['phone_primary']);
$phone_secondary = htmlspecialchars($user['phone_secondary']);

?>