<!-- 
items to print:
user_id
email
first_name
last_name
college name
phone number
trust score   
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile </title>
</head>

<body>

    <?php
    // include profile_process.php and use its variables
    include('../server/profile_process.php');
    ?>
    <h1>User Profile</h1>
    <p><strong>First Name:</strong> <?php echo $first_name; ?></p>
    <p><strong>Last Name:</strong> <?php echo $last_name; ?></p>
    <p><strong>Email:</strong> <?php echo $email; ?></p>
    <p><strong>College Name:</strong> <?php echo $college_name; ?></p>
    <p><strong>Phone Number:</strong> <?php echo $phone_primary; ?></p>
    <p><strong>Phone Number (secondary):</strong> <?php echo $phone_secondary; ?></p>
    <p><strong>Trust Score:</strong> <?php echo $trust_score; ?></p>

    <br>
    <a href="edit_profile.php">Edit profile</a>
    <br>
    <a href="logout.php">Logout</a>
</body>

</html>