<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home page</title>
</head>

<body>

    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }
    echo $_SESSION['email'] . " of id: " . $_SESSION['user_id'];
    ?>

    <h1>Welcome to the home page!</h1>
    <a href="book/add_book.php">Add a Book</a> <br>
    <a href="server/logout_process.php">Logout</a> <br>
    <a href="user/profile.php">Profile</a>
</body>

</html>