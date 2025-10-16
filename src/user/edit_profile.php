<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile</title>
</head>

<body>
    <?php
    // include profile_process.php and use its variables
    include('../server/profile_process.php');
    ?>
    <h1>Edit User Profile</h1>

    <form action="../server/edit_profile_process.php" method="post">
        <label for="first_name"><strong>First Name:</strong></label>
        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>"><br>
        <br>
        <label for="last_name"><strong>Last Name:</strong></label>
        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>"><br>
        <br>
        <label for="email"><strong>Email:</strong></label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br>
        <br>
        <label for="password"><strong>Password:</strong></label>
        <label for="password"><em>(Leave blank to keep current password)</em></label><br>
        <input type="password" id="password" name="password" value=""><br>

        <!-- confirm password -->
        <label for="password_confirm"><strong>Confirm Password:</strong></label>
        <label for="password_confirm"><em>(Leave blank to keep current password)</em></label><br>
        <input type="password" id="password_confirm" name="password_confirm" value=""><br>
        <br>
        <label for="date_of_birth"><strong>Date of Birth:</strong></label>
        <input type="date" id="date_of_birth" name="date_of_birth"
            value="<?php echo htmlspecialchars($date_of_birth); ?>"><br>
        <br>
        <label for="college_name"><strong>College Name:</strong></label>
        <input type="text" id="college_name" name="college_name"
            value="<?php echo htmlspecialchars($college_name); ?>"><br>
        <br>
        <label for="phone_primary"><strong>Phone Number:</strong></label>
        <input type="text" id="phone_primary" name="phone_primary"
            value="<?php echo htmlspecialchars($phone_primary); ?>"><br>
        <br>
        <label for="phone_secondary"><strong>Phone Number (secondary):</strong></label>
        <input type="text" id="phone_secondary" name="phone_secondary"
            value="<?php echo htmlspecialchars($phone_secondary); ?>"><br>
        <br>
        <input type="submit" value="Save Changes">
    </form>

    <br>
    <a href="profile.php"> Profile</a>
    <br>
    <a href="logout.php">Logout</a>
</body>

</html>