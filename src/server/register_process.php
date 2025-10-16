<?php

include('connection.php');
$req_attr = ['email', 'password', 'date_of_birth', 'first_name', 'last_name', 'college_name', 'phone_primary'];
foreach ($req_attr as $a) {
    if (!isset($_POST[$a]) || empty($_POST[$a])) {
        die("Please fill the " . $a . " field!");
    }
}

$email = $_POST['email'];
$pass = hash('sha256', trim($_POST['password']));
$dob = $_POST['date_of_birth'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$college_name = $_POST['college_name'];
$phone_primary = $_POST['phone_primary'];
$phone_secondary = $_POST['phone_secondary'];

if ($phone_secondary == "") {
    $phone_secondary = null;
}

// check if the email already exists
$email_check = $conn->prepare("select * from users where email = ?");
$email_check->execute([$email]);
$email_details = $email_check->get_result();
if ($email_details->num_rows > 0) {
    die("Email already registered");
}


$stmt = $conn->prepare("insert into users (email, password_hash, date_of_birth, first_name, last_name, college_name, phone_primary, phone_secondary) values (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$email, $pass, $dob, $first_name, $last_name, $college_name, $phone_primary, $phone_secondary]);

$details = $stmt->get_result();
if ($stmt->affected_rows === 1) {
    echo "User registered successfully";
    // auto login the user
    $stmt = $conn->prepare("SELECT * FROM users where email = ?");
    $stmt->execute([$email]);

    $details = $stmt->get_result();
    // echo "num of users" . $details->num_rows . "<br>";
    $user = $details->fetch_assoc();
    if ($details->num_rows === 0) {
        die("User not found");
    } else {
        // echo "email: " . $user['email'] . " <br> id: " . $user['user_id'];

        session_start();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        header("Location: ../home.php");
    }
    // echo "num of users" . $details->num_rows . "<br>";
// $user = $details->fetch_assoc();
// if ($details->num_rows === 0) {
//     die("User not found");
// } else {
//     echo "pass_____: " . $user['password_hash'] . " <br>";
//     echo "pass_hash: " . $pass . " <br>";

    //     if ($user && trim($user['password_hash']) === trim($pass)) {
//         echo "correct pass";
//         session_start();
//         $_SESSION['user_id'] = $user['user_id'];
//         $_SESSION['email'] = $user['email'];
//         header("Location: ../home.php");
//     } else {
//         die("Incorrect password");
//         header("Location: ../user/login.php");
//     }
}
?>