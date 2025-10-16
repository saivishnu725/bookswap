<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>

    <?php
    session_start();
    if (isset($_SESSION['user_id'])) {
        header("Location: ../home.php");
        exit();
    }
    ?>
    <h2>Register</h2>
    <form action="../server/register_process.php" method="post">
        <label for="email">Email:</label>
        <input type="text" name="email" id="email" value="me@example.com">
        <br>
        <label for="pass">Password:</label>
        <input type="password" name="password" id="password" value="1234567890">
        <br>
        <!-- first name -->
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" value="John">
        <br>
        <!-- last name -->
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" value="Doe">
        <br>
        <!-- dob -->
        <label for="dob">Date of Birth:</label>
        <input type="date" name="date_of_birth" id="date_of_birth" value="2000-01-01">
        <br>
        <!-- college name -->
        <label for="college_name">College Name:</label>
        <input type="text" name="college_name" id="college_name" value="MIT">
        <br>
        <!-- phone number primary -->
        <label for="phone_primary">Primary Phone Number:</label>
        <input type="tel" name="phone_primary" id="phone_primary" value="123-456-7890" required>
        <br>
        <!-- phone numer secondary -->
        <label for="phone_secondary">Secondary Phone Number:</label>
        <input type="tel" name="phone_secondary" id="phone_secondary" value="098-765-4321" required>
        <br>

        <input type="submit" value="Register">
    </form>
</body>

</html>