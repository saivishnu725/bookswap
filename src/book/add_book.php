<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book - Student BookSwap</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">📚 BookSwap</a>
            <div class="nav-menu" id="navMenu">
                <a href="../user/profile.php" class="nav-link">Profile</a>
                <a href="../index.php" class="nav-link">Browse</a>
                <div class="nav-auth" id="navAuth"></div>
            </div>
            <button class="mobile-toggle" id="mobileToggle">☰</button>
        </div>
    </nav>

    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }
    ?>

    <div class="page-header">
        <h1>Add a New Book</h1>
        <p>List your textbook for sale to fellow students</p>
    </div>

    <div class="container">
        <div class="form-container">
            <form action="../server/add_book_process.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" id="title" name="title" class="form-input" placeholder="Enter book title"
                        required>
                </div>

                <div class="form-group">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" id="author" name="author" class="form-input" placeholder="Enter author name"
                        required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea" rows="4"
                        placeholder="Provide a detailed description of the book's condition, edition, and any notes for buyers"
                        required></textarea>
                </div>

                <div class="form-group">
                    <label for="condition" class="form-label">Condition</label>
                    <select id="condition" name="condition" class="form-select" required>
                        <option value="">Select condition</option>
                        <option value="NEW">New</option>
                        <option value="GOOD">Good</option>
                        <option value="FAIR">Fair</option>
                        <option value="POOR">Poor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="year_of_purchase" class="form-label">Year of Purchase</label>
                    <input type="number" id="year_of_purchase" name="year_of_purchase" class="form-input" min="1900"
                        max="2025" placeholder="e.g., 2022" required>
                </div>

                <div class="form-group">
                    <label for="cost_at_purchase" class="form-label">Cost at Purchase (₹)</label>
                    <input type="number" step="5" id="cost_at_purchase" name="cost_at_purchase" class="form-input"
                        placeholder="Original price you paid" required>
                </div>

                <div class="form-group">
                    <label for="current_selling_price" class="form-label">Current Selling Price (₹)</label>
                    <input type="number" step="5" id="current_selling_price" name="current_selling_price"
                        class="form-input" placeholder="Your asking price" required>
                </div>

                <div class="form-group">
                    <label for="negotiable" class="form-label">Negotiable</label>
                    <select id="negotiable" name="negotiable" class="form-select" required>
                        <option value="">Select option</option>
                        <option value="YES">Yes</option>
                        <option value="NO">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image" class="form-label">Book Image</label>
                    <input type="file" id="image" name="image" class="form-input" accept="image/*">
                    <small style="color: var(--muted-foreground);">Upload a clear photo of your book (JPG, PNG, max
                        5MB)</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                    Add Book to Marketplace
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Student BookSwap. A peer-to-peer book exchange platform for students.</p>
        </div>
    </footer>
</body>

</html>