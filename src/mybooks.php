<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Books - Student BookSwap</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">📚 BookSwap</a>
            <div class="nav-menu" id="navMenu">
                <a href="./user/profile.php" class="nav-link">Profile</a>
                <a href="./home.php" class="nav-link">Browse</a>
                <a href="./server/logout_process.php" class="nav-link">Logout</a>
            </div>
            <button class="mobile-toggle" id="mobileToggle">☰</button>
        </div>
    </nav>

    <?php
    // session_start();
    // if (!isset($_SESSION['user_id'])) {
    //     header("Location: index.php");
    //     exit();
    // }
    
    include('server/mybooks_process.php');
    ?>

    <div class="page-header">
        <h1>My Books</h1>
        <p>Manage your listed books for sale</p>
    </div>

    <div class="container">
        <div class="books-management">
            <!-- Add Book Button -->
            <a href="./book/add_book.php" class="btn btn-primary" style="margin-bottom: 2rem; display: inline-block;">
                ➕ Add New Book
            </a>

            <!-- Books List -->
            <div class="books-section">
                <?php if (empty($books_array)): ?>
                    <div class="alert alert-info">
                        You haven't listed any books yet. Click "Add New Book" to get started!
                    </div>
                <?php else: ?>
                    <div class="books-grid">
                        <?php foreach ($books_array as $book): ?>
                            <div class="book-card">
                                <!-- Book Image Placeholder -->
                                <div class="book-image"
                                    style="background: var(--muted); display: flex; align-items: center; justify-content: center; color: var(--muted-foreground);">
                                    📚 No Image
                                </div>
                                <div class="book-content">
                                    <h3 class="book-title"><?php echo $book['book_name']; ?></h3>
                                    <p class="book-author">Description: <?php echo $book['descr']; ?></p>
                                    <div class="book-details">
                                        <span class="book-price">₹<?php echo $book['current_selling_price']; ?></span>
                                        <span class="book-condition"><?php echo $book['condition']; ?></span>
                                    </div>
                                    <div class="action-buttons" style="margin-top: 1rem;">
                                        <button class="btn btn-outline"
                                            onclick="editBook(<?php echo $book['book_id']; ?>)">Edit</button>
                                        <button class="btn btn-danger"
                                            onclick="confirmDeleteBook(<?php echo $book['book_id']; ?>)">Delete</button>
                                    </div>
                                    <div style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--muted-foreground);">
                                        <p>Purchase Year: <?php echo $book['year_of_purchase']; ?></p>
                                        <p>Original Cost: ₹<?php echo $book['cost_at_purchase']; ?></p>
                                        <p>Negotiable: <?php echo $book['negotiation'] === 'YES' ? 'Yes' : 'No'; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Student BookSwap. A peer-to-peer book exchange platform for students.</p>
        </div>
    </footer>

    <script>
        function editBook(bookId) {
            // Redirect to edit book page or show edit form
            alert('Edit functionality for book ID: ' + bookId + ' will be implemented here');
            // You can redirect to: window.location.href = 'edit_book.php?id=' + bookId;
        }

        function confirmDeleteBook(bookId) {
            if (confirm('Are you sure you want to delete this book listing?')) {
                // Redirect to delete process
                window.location.href = 'server/delete_book_process.php?id=' + bookId;
            }
        }
    </script>
</body>

</html>