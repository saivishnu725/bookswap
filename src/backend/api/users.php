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

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username']);
$password = trim($input['password']);

$sql = "SELECT id, email, password FROM users WHERE email = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("s", $param_username);
    $param_username = $username;
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $stmt->bind_result($id, $username, $hashed_password);
            if ($stmt->fetch()) {
                if ($password == $hashed_password) {
                    session_regenerate_id();
                    $_SESSION['loggedin'] = TRUE;
                    $_SESSION['email'] = $username;
                    $_SESSION['id'] = $id;
                }
                http_response_code(200);
                echo json_encode(array("message" => "Login successful"));
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Invalid username or password"));
            }

            $stmt->close();
        }
    }
}
?>