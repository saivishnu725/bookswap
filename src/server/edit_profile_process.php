<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include('connection.php');
$stmt = $conn->prepare("SELECT email, first_name, last_name, date_of_birth, college_name, phone_primary, phone_secondary FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$details = $stmt->get_result();
if ($details->num_rows === 0) {
    die("User not found");
} else {
    $current = $details->fetch_assoc();
    $new = [
        'email' => trim($_POST['email']),
        'first_name' => trim($_POST['first_name']),
        'last_name' => trim($_POST['last_name']),
        'date_of_birth' => trim($_POST['date_of_birth']),
        'college_name' => trim($_POST['college_name']),
        'phone_primary' => trim($_POST['phone_primary']),
        'phone_secondary' => trim($_POST['phone_secondary'])
    ];

    $pass_new = trim($_POST['password']);
    $pass_confirm = trim($_POST['password_confirm']);

    $changed = [];

    //  compare new vs old
    foreach ($new as $key => $val) {
        if ($val !== $current[$key]) {
            $changed[$key] = $val;
        }
    }

    // handle password change
    if (!empty($pass_new) && $pass_new === $pass_confirm) {
        $changed['password'] = hash('sha256', $pass_new);
    }
    if (!empty($changed)) {
        $setParts = [];
        $params = [];

        foreach ($changed as $col => $value) {
            $setParts[] = "$col = ?";
            $params[] = $value;
        }

        $params[] = $_SESSION['user_id'];

        $sql = "UPDATE users SET " . implode(',', $setParts) . " WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }
    echo "Profile updated successfully";
    // changed
    echo "<br>Changed fields: " . implode(", ", array_keys($changed));
    ?>
    <br>
    <a href="../user/profile.php">Go back to profile</a>
    <?php
    // header("Location: ../user/profile.php");
}
?>