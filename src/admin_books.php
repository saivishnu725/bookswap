<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Student BookSwap Admin</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">📚 BookSwap Admin</a>
            <div class="nav-menu" id="navMenu">
                <a href="index.php" class="nav-link">Main Site</a>
                <a href="admin.php" class="nav-link">Dashboard</a>
                <a href="admin_users.php" class="nav-link">Users</a>
                <a href="admin_books.php" class="nav-link">Books</a>
                <a href="admin_transactions.php" class="nav-link">Transactions</a>
                <a href="server/logout_process.php" class="nav-link">Logout</a>
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
        <h1>Manage Books</h1>
        <p>View and manage all book listings</p>
    </div>

    <div class="container" style="padding: 2rem 1rem;">
        <!-- Search and Filters -->
        <div class="filters">
            <div class="filter-group">
                <input type="text" id="searchQuery" placeholder="Search by book title, author..." class="form-input"
                    onkeyup="debounceSearch()">
            </div>

            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <div class="filter-group">
                    <label class="form-label">Status</label>
                    <select id="statusFilter" class="form-select" onchange="loadBooks()">
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="sold">Sold</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="form-label">Condition</label>
                    <select id="conditionFilter" class="form-select" onchange="loadBooks()">
                        <option value="">All Conditions</option>
                        <option value="NEW">New</option>
                        <option value="GOOD">Good</option>
                        <option value="FAIR">Fair</option>
                        <option value="POOR">Poor</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                <button onclick="loadBooks()" class="btn btn-primary">Search</button>
                <button onclick="clearFilters()" class="btn btn-outline">Clear</button>
            </div>
        </div>

        <!-- Results Count -->
        <div style="margin: 1rem 0;">
            <p id="resultsCount" style="color: var(--muted-foreground);">Loading books...</p>
        </div>

        <!-- Books Table -->
        <div id="booksTable" style="background: var(--card); border-radius: var(--radius); overflow: hidden;">
            <div class="alert alert-info">Loading books...</div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Student BookSwap. Admin Dashboard</p>
        </div>
    </footer>

    <script>
        let searchTimeout;

        document.addEventListener('DOMContentLoaded', function () {
            loadBooks();
        });

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadBooks();
            }, 500);
        }

        function loadBooks() {
            const search = document.getElementById('searchQuery').value;
            const status = document.getElementById('statusFilter').value;
            const condition = document.getElementById('conditionFilter').value;

            const params = new URLSearchParams({
                action: 'get_books',
                search: search,
                status: status,
                condition: condition
            });

            fetch(`server/admin_books_process.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayBooks(data.books);
                        document.getElementById('resultsCount').textContent =
                            `Found ${data.books.length} books`;
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('booksTable').innerHTML =
                        `<div class="alert alert-error">Error loading books: ${error.message}</div>`;
                });
        }

        function clearFilters() {
            document.getElementById('searchQuery').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('conditionFilter').value = '';
            loadBooks();
        }

        function displayBooks(books) {
            const container = document.getElementById('booksTable');

            if (books.length === 0) {
                container.innerHTML = '<div class="alert alert-info">No books found</div>';
                return;
            }

            container.innerHTML = `
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: var(--muted);">
                            <tr>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Book ID</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Title</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Author</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Seller</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Price</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Condition</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Status</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${books.map(book => `
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 1rem;">${book.book_id}</td>
                                    <td style="padding: 1rem;">
                                        <strong>${escapeHtml(book.book_name)}</strong>
                                    </td>
                                    <td style="padding: 1rem;">${escapeHtml(book.author_name || 'N/A')}</td>
                                    <td style="padding: 1rem;">
                                        ${escapeHtml(book.seller_name)}<br>
                                        <small style="color: var(--muted-foreground);">${escapeHtml(book.seller_college)}</small>
                                    </td>
                                    <td style="padding: 1rem;">₹${book.current_selling_price}</td>
                                    <td style="padding: 1rem;">
                                        <span class="book-condition">${book.condition}</span>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span style="padding: 0.25rem 0.5rem; border-radius: 20px; font-size: 0.875rem; 
                                            background: ${book.status === 'sold' ? '#d1fae5' : '#dbeafe'}; 
                                            color: ${book.status === 'sold' ? '#065f46' : '#1e40af'};">
                                            ${book.status.toUpperCase()}
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <button onclick="viewBookDetails(${book.book_id})" class="btn btn-outline" style="padding: 0.5rem 1rem; margin-bottom: 0.25rem;">
                                            View
                                        </button>
                                        <button onclick="deleteBook(${book.book_id})" class="btn btn-danger" style="padding: 0.5rem 1rem;">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function viewBookDetails(bookId) {
            window.open(`./book/book.php?id=${bookId}`, '_blank');
        }

        function deleteBook(bookId) {
            if (confirm('Are you sure you want to delete this book? This action cannot be undone.')) {
                fetch(`server/admin_books_process.php?action=delete_book&book_id=${bookId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Book deleted successfully');
                            loadBooks();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting book');
                    });
            }
        }

        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>

</html>