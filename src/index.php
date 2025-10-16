<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>start page</title>
</head>

<body>

    <?php
    session_start();
    if (isset($_SESSION['user_id'])) {
        echo "You are already logged in." . $_SESSION['user_id'];
        header("Location: home.php");
        exit();
    }
    ?>

    <h1>login or register</h1>
    <a href="user/login.php">login</a>
    <br>
    <a href="user/register.php">register</a>
</body>

</html>