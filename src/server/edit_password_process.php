<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// db connection
include('connection.php');

// check if user exists
$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$details = $stmt->get_result();
if ($details->num_rows === 0) {
    die("User not found");
}

// if user exists, process
$pass_new = trim($_POST['password']);
$pass_confirm = trim($_POST['password_confirm']);
echo "Pass new: " . $pass_new . "<br>";
echo "Pass confirm: " . $pass_confirm . "<br>";
$pass_hashed = "-1";

// handle password change
if (!empty($pass_new) && $pass_new === $pass_confirm) {
    $pass_hashed = hash('sha256', $pass_new);
} else {
    die("Password fields are either empty or do not match.");
}

$params[] = $_SESSION['user_id'];

$sql = "UPDATE users SET password_hash='" . $pass_hashed . "' WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
echo "Profile updated successfully";
?>
<br>
<a href="../user/profile.php">Go back to profile</a>
<?php
header("Location: ../user/profile.php");
?>