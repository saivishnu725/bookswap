<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student BookSwap</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php
    session_start();
    if (isset($_SESSION['user_id'])) {
        header("Location: ../home.php");
        // echo "You are already logged in as " . $_SESSION['user_id'];
        exit();
    }
    ?>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.html" class="logo">📚 BookSwap</a>
            <div class="nav-menu" id="navMenu">
                <a href="../index.php" class="nav-link">Home</a>
                <a href="./register.php" class="nav-link">Register</a>
                <div class="nav-auth" id="navAuth"></div>
            </div>
            <button class="mobile-toggle" id="mobileToggle">☰</button>
        </div>
    </nav>

    <div class="form-container">
        <h1 class="form-title">Login</h1>

        <div id="errorMessage"></div>
        <form id="loginForm" action="../server/login_process.php" method="post">

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" required
                    placeholder="your.email@student.edu">
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" required
                    placeholder="Enter your password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Login
            </button>
        </form>

        <div class="form-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>

        <div class="alert alert-info" style="margin-top: 2rem;">
            <strong>Demo Account:</strong><br>
            Email: sai@student.manipal.edu<br>
            Password: CHANGE_THIS_PASSWORD
        </div>
    </div>

</body>

</html>