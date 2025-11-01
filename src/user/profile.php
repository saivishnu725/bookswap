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
    <title>My Profile - Student BookSwap</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php
    // Include the PHP file that fetches user data
    include('../server/profile_process.php');

    // Combine first_name and last_name for the 'Full Name' field
    $full_name = htmlspecialchars($first_name . ' ' . $last_name);
    // Sanitize and prepare other variables for display
    $email_display = htmlspecialchars($email);
    $college_name_display = htmlspecialchars($college_name);
    $phone_primary_display = htmlspecialchars($phone_primary);
    $phone_secondary_display = htmlspecialchars($phone_secondary);
    $trust_score_display = htmlspecialchars($trust_score);
    $date_of_birth_display = htmlspecialchars($date_of_birth);
    ?>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.html" class="logo">📚 BookSwap</a>
            <div class="nav-menu" id="navMenu">
                <!-- <a href="../index.php" class="nav-link">Home</a> -->
                <a href="../server/logout_process.php" class="nav-link">Logout</a>
                <a href="../home.php" class="nav-link">Browse</a>
                <div class="nav-auth" id="navAuth"></div>
            </div>
            <button class="mobile-toggle" id="mobileToggle">☰</button>
        </div>
    </nav>

    <div class="page-header">
        <h1>My Profile</h1>
        <p>Manage your account information</p>
    </div>

    <div class="profile-section">

        <div class="form-container">
            <h2>Your Details</h2>

            <!-- <div class="info-group">
                <p class="form-label"><strong>User ID:</strong></p>
                <p><?php echo htmlspecialchars($user_id); ?></p>
            </div> -->
            <div class="info-group">
                <p class="form-label"><strong>Full Name:</strong></p>
                <p><?php echo $full_name; ?></p>
            </div>
            <div class="info-group">
                <p class="form-label"><strong>Email:</strong></p>
                <p><?php echo $email_display; ?></p>
            </div>
            <div class="info-group">
                <p class="form-label"><strong>Date of Birth:</strong></p>
                <p><?php echo $date_of_birth_display; ?></p>
            </div>
            <div class="info-group">
                <p class="form-label"><strong>College Name:</strong></p>
                <p><?php echo $college_name_display; ?></p>
            </div>
            <div class="info-group">
                <p class="form-label"><strong>Primary Phone:</strong></p>
                <p><?php echo $phone_primary_display; ?></p>
            </div>
            <?php if (!empty($phone_secondary_display)): ?>
                <div class="info-group">
                    <p class="form-label"><strong>Secondary Phone:</strong></p>
                    <p><?php echo $phone_secondary_display; ?></p>
                </div>
            <?php endif; ?>

            <a href="edit_profile.php" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                Edit Profile Information
            </a>
        </div>

        <hr style="margin: 2rem 0;">

        <div class="form-container">
            <h2>Account Statistics</h2>
            <div class="info-group">
                <p class="form-label"><strong>Trust Score:</strong></p>
                <p><?php echo $trust_score_display; ?></p>
            </div>
            <div id="accountStats"></div>
        </div>

        <hr style="margin: 2rem 0;">

        <div class="form-container" style="margin-top: 2rem;">
            <h2>Account Actions</h2>

            <a href="../server/logout_process.php" class="btn btn-secondary" style="width: 100%; margin-bottom: 1rem;">
                Logout
            </a>

            <button onclick="confirm_delete()" class="btn btn-danger" style="width: 100%;">
                Delete Account
            </button>

        </div>
    </div>

    <script>
        function confirm_delete() {
            if (confirm("🚨 Are you absolutely sure you want to delete your account? This action is permanent and cannot be undone.")) {
                // Create a form and submit it as POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../server/delete_user_process.php';
                document.body.appendChild(form);
                form.submit();
            } else {
                alert("Account deletion canceled!");
            }
        }
    </script>

</body>

</html>