<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>

<body>

    <?php
    session_start();
    if (isset($_SESSION['user_id'])) {
        // header("Location: ../home.php");
        echo "You are already logged in." . $_SESSION['user_id'];
        // exit();
    }
    ?>

    <h2>login</h2>
    <form action="../server/login_process.php" method="post">
        <label for="email">email</label>
        <input type="text" name="email" id="email" value="me@example.com" required>

        <label for="pass">password</label>
        <input type="pass" name="password" id="password" value="1234567890" required>

        <input type="submit" value="Log in">

    </form>

</body>

</html>