<?php
session_start();
if (isset($_SESSION['user_id'])) {
    echo "You are already logged in." . $_SESSION['user_id'];
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student BookSwap - Buy and Sell Academic Books</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.html" class="logo">📚 BookSwap</a>
            <div class="nav-menu" id="navMenu">
                <a href="./user/login.php" class="nav-link">Login</a>
                <a href="./user/register.php" class="nav-link">Register</a>
                <div class="nav-auth" id="navAuth"></div>
            </div>
            <button class="mobile-toggle" id="mobileToggle">☰</button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="hero-title">Student BookSwap</h1>
            <p class="hero-subtitle">
                Buy and sell used academic books directly from your peers. Save money, help others, and reduce waste.
            </p>

            <!-- CTA Buttons -->
            <div class="cta-buttons">
                <a href="./home.php" class="btn btn-primary">Browse Books →</a>
                <a href="./user/register.php" class="btn btn-outline" id="ctaButton">Get Started</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose BookSwap?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📖</div>
                    <h3>Affordable Books</h3>
                    <p>Save up to 70% on textbooks by buying directly from fellow students</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Trusted Community</h3>
                    <p>Buy and sell within your campus community for safe transactions</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Easy & Secure</h3>
                    <p>Simple listing process with direct peer-to-peer communication</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Student BookSwap. A peer-to-peer book exchange platform for students.</p>
        </div>
    </footer>
</body>

</html>