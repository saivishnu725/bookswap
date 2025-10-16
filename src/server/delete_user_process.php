<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    include('connection.php');

    $user_id = $_SESSION['user_id'];
    try {
        // Prepare and execute the delete query
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        echo "User deleted successfully.";
        header("Location: logout_process.php");
    } catch (\Throwable $th) {
        echo "Error deleting user: " . $th->getMessage();
        exit();
    }

}
?>