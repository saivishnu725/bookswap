<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Student BookSwap</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">📚 BookSwap</a>
            <div class="nav-menu" id="navMenu">
                <a href="../index.php" class="nav-link">Home</a>
                <a href="../browse.php" class="nav-link">Browse</a>
                <a href="../mybooks.php" class="nav-link">My Books</a>
                <a href="../interested_books.php" class="nav-link">Interests</a>
                <a href="../server/logout_process.php" class="nav-link">Logout</a>
            </div>
            <button class="mobile-toggle" id="mobileToggle">☰</button>
        </div>
    </nav>

    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }

    // Load book data directly here instead of including the process file
    if (isset($_GET['id'])) {
        include('../server/connection.php');

        $book_id = intval($_GET['id']);
        $user_id = $_SESSION['user_id'];

        try {
            $query = "SELECT 
                        b.*,
                        COALESCE(bi.image_url, 'assets/images/default-book.png') as image_url
                    FROM books b 
                    LEFT JOIN book_images bi ON b.book_id = bi.book_id 
                    WHERE b.book_id = ? AND b.seller_id = ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $book_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $_SESSION['error'] = "Book not found or you are not authorized to edit it";
                header("Location: ../mybooks.php");
                exit();
            }

            $book_data = $result->fetch_assoc();
            $stmt->close();
            $conn->close();

        } catch (Exception $e) {
            $_SESSION['error'] = "Server error: " . $e->getMessage();
            header("Location: ../mybooks.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "No book specified";
        header("Location: ../mybooks.php");
        exit();
    }
    ?>

    <div class="page-header">
        <h1>Edit Book</h1>
        <p>Update your book listing</p>
    </div>

    <div class="container">
        <div class="form-container">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="../server/edit_book_process.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="book_id" value="<?php echo $book_data['book_id']; ?>">

                <div class="form-group">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" id="title" name="title" class="form-input"
                        value="<?php echo htmlspecialchars($book_data['book_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" id="author" name="author" class="form-input"
                        value="<?php echo htmlspecialchars($book_data['author_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea" rows="4"
                        required><?php echo htmlspecialchars($book_data['descr']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="condition" class="form-label">Condition</label>
                    <select id="condition" name="condition" class="form-select" required>
                        <option value="">Select condition</option>
                        <option value="NEW" <?php echo $book_data['condition'] === 'NEW' ? 'selected' : ''; ?>>New
                        </option>
                        <option value="GOOD" <?php echo $book_data['condition'] === 'GOOD' ? 'selected' : ''; ?>>Good
                        </option>
                        <option value="FAIR" <?php echo $book_data['condition'] === 'FAIR' ? 'selected' : ''; ?>>Fair
                        </option>
                        <option value="POOR" <?php echo $book_data['condition'] === 'POOR' ? 'selected' : ''; ?>>Poor
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="year_of_purchase" class="form-label">Year of Purchase</label>
                    <input type="number" id="year_of_purchase" name="year_of_purchase" class="form-input" min="1900"
                        max="2025" value="<?php echo $book_data['year_of_purchase']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="cost_at_purchase" class="form-label">Cost at Purchase (₹)</label>
                    <input type="number" step="5" id="cost_at_purchase" name="cost_at_purchase" class="form-input"
                        value="<?php echo $book_data['cost_at_purchase']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="current_selling_price" class="form-label">Current Selling Price (₹)</label>
                    <input type="number" step="5" id="current_selling_price" name="current_selling_price"
                        class="form-input" value="<?php echo $book_data['current_selling_price']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="negotiable" class="form-label">Negotiable</label>
                    <select id="negotiable" name="negotiable" class="form-select" required>
                        <option value="">Select option</option>
                        <option value="YES" <?php echo $book_data['negotiation'] === 'YES' ? 'selected' : ''; ?>>Yes
                        </option>
                        <option value="NO" <?php echo $book_data['negotiation'] === 'NO' ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="available" <?php echo ($book_data['status'] ?? 'available') === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="sold" <?php echo ($book_data['status'] ?? 'available') === 'sold' ? 'selected' : ''; ?>>Sold</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image" class="form-label">Book Image</label>
                    <?php if (!empty($book_data['image_url']) && $book_data['image_url'] !== 'assets/images/default-book.png'): ?>
                        <div style="margin-bottom: 1rem;">
                            <img src="../<?php echo $book_data['image_url']; ?>" alt="Current book image"
                                style="max-width: 200px; border-radius: var(--radius);">
                            <p style="font-size: 0.875rem; color: var(--muted-foreground); margin-top: 0.5rem;">
                                Current image
                            </p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" class="form-input" accept="image/*">
                    <small style="color: var(--muted-foreground);">Leave empty to keep current image</small>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">Update Book</button>
                    <a href="../mybooks.php" class="btn btn-outline">Cancel</a>
                </div>
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