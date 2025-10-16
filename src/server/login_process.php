<?php

include('connection.php');

if (!isset($_POST['email']) || !isset($_POST['password'])) {
    die("Please fill both the email and password fields!");
}

$email = $_POST['email'];
$pass = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users where email = ?");
$stmt->execute([$email]);

$details = $stmt->get_result();
echo "num of users" . $details->num_rows . "<br>";
$user = $details->fetch_assoc();
if ($details->num_rows === 0) {
    die("User not found");
} else {
    $pass = trim($pass);
    $pass = hash("sha256", $pass);
    echo "pass_saved: " . $user['password_hash'] . " <br>";
    echo "pass_form : " . $pass . " <br>";

    if ($user && trim($user['password_hash']) === trim($pass)) {
        echo "correct pass";
        session_start();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        header("Location: ../home.php");
    } else {
        die("Incorrect password");
    }
}
?>