<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Student BookSwap</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">📚 BookSwap</a>
            <div class="nav-menu" id="navMenu">
                <a href="../index.php" class="nav-link">Home</a>
                <a href="./login.php" class="nav-link">Login</a>
                <div class="nav-auth" id="navAuth"></div>
            </div>
            <button class="mobile-toggle" id="mobileToggle">☰</button>
        </div>
    </nav>

    <!-- Registration Form -->
    <div class="form-container">
        <h1 class="form-title">Register</h1>

        <form action="../server/register_process.php" method="post" id="registerForm">

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="your.email@student.edu"
                    required>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Create a password"
                    required>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label class="form-label" for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" class="form-input" placeholder="Confirm your password"
                    required>
            </div>

            <!-- First Name -->
            <div class="form-group">
                <label class="form-label" for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-input" placeholder="John" required>
            </div>

            <!-- Last Name -->
            <div class="form-group">
                <label class="form-label" for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-input" placeholder="Doe" required>
            </div>

            <!-- Date of Birth -->
            <div class="form-group">
                <label class="form-label" for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" class="form-input" required>
            </div>

            <!-- College Name -->
            <div class="form-group">
                <label class="form-label" for="college_name">College Name</label>
                <input type="text" id="college_name" name="college_name" class="form-input"
                    placeholder="Your College Name" required>
            </div>

            <!-- Primary Phone -->
            <div class="form-group">
                <label class="form-label" for="phone_primary">Primary Phone Number</label>
                <input type="tel" id="phone_primary" name="phone_primary" class="form-input"
                    placeholder="+91-1234567890" required>
            </div>

            <!-- Secondary Phone -->
            <div class="form-group">
                <label class="form-label" for="phone_secondary">Secondary Phone Number</label>
                <input type="tel" id="phone_secondary" name="phone_secondary" class="form-input"
                    placeholder="+91-0987654321">
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Register
            </button>
        </form>

        <div class="form-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

    <script>
        // password validation
        document.getElementById('registerForm').addEventListener('submit', (e) => {
            const pass = document.getElementById('password').value.trim();
            const confirm = document.getElementById('confirmPassword').value.trim();

            if (pass !== confirm) {
                e.preventDefault();
                alert("Passwords do not match!");
                return;
            }
            if (pass.length < 6) {
                e.preventDefault();
                alert("Password must be at least 6 characters long.");
                return;
            }
        });
    </script>
</body>

</html>