<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Student BookSwap</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php
    // Include the PHP file that fetches user data
    include('../server/profile_process.php');

    // Sanitize and prepare variables for input values
    $first_name_val = htmlspecialchars($first_name);
    $last_name_val = htmlspecialchars($last_name);
    $email_val = htmlspecialchars($email);
    $date_of_birth_val = htmlspecialchars($date_of_birth);
    $college_name_val = htmlspecialchars($college_name);
    $phone_primary_val = htmlspecialchars($phone_primary);
    $phone_secondary_val = htmlspecialchars($phone_secondary);
    ?>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.html" class="logo">📚 BookSwap</a>
            <div class="nav-menu">
                <a href="profile.php" class="nav-link">My Profile</a>
                <a href="../home.php" class="nav-link">Browse</a>
                <a href="../server/logout_process.php" class="nav-link">Logout</a>
            </div>
            <button class="mobile-toggle" id="mobileToggle">☰</button>
        </div>
    </nav>

    <div class="page-header">
        <h1>Edit Profile</h1>
        <p>Update your personal and contact information.</p>
    </div>

    <div class="profile-section">

        <div class="form-container">
            <form action="../server/edit_profile_process.php" method="post">

                <h2>Personal Information</h2>

                <div class="form-group">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-input"
                        value="<?php echo $first_name_val; ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-input"
                        value="<?php echo $last_name_val; ?>" required>
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-input"
                        value="<?php echo $date_of_birth_val; ?>">
                </div>

                <hr style="margin: 2rem 0;">

                <h2>Contact & Academic Details</h2>

                <div class="form-group">
                    <label for="email" class="form-label">Email (Read-only)</label>
                    <input type="email" id="email" name="email" class="form-input" value="<?php echo $email_val; ?>"
                        readonly style="background: var(--muted); cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label for="college_name" class="form-label">College Name</label>
                    <input type="text" id="college_name" name="college_name" class="form-input"
                        value="<?php echo $college_name_val; ?>">
                </div>

                <div class="form-group">
                    <label for="phone_primary" class="form-label">Primary Phone Number</label>
                    <input type="text" id="phone_primary" name="phone_primary" class="form-input"
                        value="<?php echo $phone_primary_val; ?>">
                </div>

                <div class="form-group">
                    <label for="phone_secondary" class="form-label">Secondary Phone Number (Optional)</label>
                    <input type="text" id="phone_secondary" name="phone_secondary" class="form-input"
                        value="<?php echo $phone_secondary_val; ?>">
                </div>

                <hr style="margin: 2rem 0;">

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Save Profile Changes
                </button>
            </form>
        </div>

        <div class="form-container" style="margin-top: 2rem;">
            <h2>Change Password</h2>

            <form action="../server/edit_password_process.php" method="post" id="passwordForm" autocomplete="off">
                <p style="margin-bottom: 1rem;">*Only fill these fields to update your password.</p>

                <!-- Hidden flag so the server-side script can reliably detect this form submission -->
                <input type="hidden" name="change_password" value="1">

                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" id="password" name="password" class="form-input"
                        autocomplete="new-password" placeholder="Leave blank to keep current password">
                </div>

                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirm New Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input"
                        autocomplete="new-password" placeholder="Re-enter new password">
                </div>

                <button type="submit" name="update_password" class="btn btn-secondary" style="width: 100%;">
                    Update Password
                </button>
            </form>
        </div>

        <div class="back-link" style="text-align: center; margin-top: 2rem;">
            <a href="profile.php">← Back to Profile</a>
        </div>
    </div>

</body>

</html>