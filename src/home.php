<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - Student BookSwap</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">📚 BookSwap</a>
            <div class="nav-menu" id="navMenu">
                <a href="./index.php" class="nav-link">Home</a>
                <a href="./user/profile.php" class="nav-link">Profile</a>
                <a href="./mybooks.php" class="nav-link">My Books</a>
                <a href="./interested_books.php" class="nav-link">Interests</a>
                <a href="./server/logout_process.php" class="nav-link">Logout</a>
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
        <h1>Browse Books</h1>
        <p>Find the perfect textbook for your courses</p>
    </div>

    <div class="container" style="padding: 2rem 1rem;">
        <!-- Search and Filters -->
        <div class="filters">
            <!-- Main Search -->
            <div class="filter-group">
                <input type="text" id="searchQuery" placeholder="Search by book title, author name..."
                    class="form-input" onkeyup="debounceSearch()">
            </div>

            <!-- Filter Row -->
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <div class="filter-group">
                    <label class="form-label">Max Price (₹)</label>
                    <input type="number" id="priceFilter" placeholder="e.g., 1000" class="form-input"
                        onchange="applyFilters()">
                </div>

                <div class="filter-group">
                    <label class="form-label">Condition</label>
                    <select id="conditionFilter" class="form-select" onchange="applyFilters()">
                        <option value="">All Conditions</option>
                        <option value="NEW">New</option>
                        <option value="GOOD">Good</option>
                        <option value="FAIR">Fair</option>
                        <option value="POOR">Poor</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="form-label">Negotiable</label>
                    <select id="negotiableFilter" class="form-select" onchange="applyFilters()">
                        <option value="">All</option>
                        <option value="YES">Yes</option>
                        <option value="NO">No</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="form-label">Sort By</label>
                    <select id="sortFilter" class="form-select" onchange="applyFilters()">
                        <option value="newest">Newest First</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="name">Book Name A-Z</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                <button onclick="applyFilters()" class="btn btn-primary">Apply Filters</button>
                <button onclick="clearFilters()" class="btn btn-outline">Clear All</button>
            </div>
        </div>

        <!-- Results Count and Loading -->
        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <p id="resultsCount" style="color: var(--muted-foreground);">Loading books...</p>
            <div id="loadingSpinner" style="display: none;">
                <span style="color: var(--primary);">Loading...</span>
            </div>
        </div>

        <!-- Books Grid -->
        <div id="booksGrid" class="books-grid">
            <div class="alert alert-info">Loading books...</div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Student BookSwap. A peer-to-peer book exchange platform for students.</p>
        </div>
    </footer>

    <script>
        let searchTimeout;

        // Load books when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadBooks();
        });

        // Debounce search to avoid too many requests
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        }

        function loadBooks(filters = {}) {
            showLoading(true);

            const formData = new FormData();
            formData.append('action', 'get_books');

            // Add all filters to form data
            const allFilters = getAllFilters();
            for (const key in allFilters) {
                if (allFilters[key]) {
                    formData.append(key, allFilters[key]);
                }
            }

            fetch('./server/home_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
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
                    document.getElementById('booksGrid').innerHTML =
                        `<div class="alert alert-error">Error loading books: ${error.message}</div>`;
                    document.getElementById('resultsCount').textContent = 'Error loading books';
                })
                .finally(() => {
                    showLoading(false);
                });
        }

        function getAllFilters() {
            return {
                search: document.getElementById('searchQuery').value.trim(),
                max_price: document.getElementById('priceFilter').value,
                condition: document.getElementById('conditionFilter').value,
                negotiable: document.getElementById('negotiableFilter').value,
                sort: document.getElementById('sortFilter').value
            };
        }

        function applyFilters() {
            loadBooks();
        }

        function clearFilters() {
            document.getElementById('searchQuery').value = '';
            document.getElementById('priceFilter').value = '';
            document.getElementById('conditionFilter').value = '';
            document.getElementById('negotiableFilter').value = '';
            document.getElementById('sortFilter').value = 'newest';
            loadBooks();
        }

        function showLoading(show) {
            document.getElementById('loadingSpinner').style.display = show ? 'block' : 'none';
            if (show) {
                document.getElementById('resultsCount').textContent = 'Loading...';
            }
        }

        function displayBooks(books) {
            const booksGrid = document.getElementById('booksGrid');

            if (books.length === 0) {
                booksGrid.innerHTML = '<div class="alert alert-info">No books found matching your criteria.</div>';
                return;
            }

            booksGrid.innerHTML = books.map(book => `
        <div class="book-card">
            <img src="${book.image_url}" alt="${escapeHtml(book.book_name)}" class="book-image" 
                onerror="this.src='assets/images/default-book.png'">
            <div class="book-content">
                <h3 class="book-title">${escapeHtml(book.book_name)}</h3>
                <p class="book-author">by ${escapeHtml(book.first_name)} ${escapeHtml(book.last_name)}</p>
                <p class="book-description" style="color: var(--muted-foreground); font-size: 0.9rem; margin-bottom: 0.5rem;">
                    ${book.descr ? escapeHtml(book.descr.substring(0, 100) + (book.descr.length > 100 ? '...' : '')) : 'No description available'}
                </p>
                <div class="book-details">
                    <span class="book-price">₹${book.current_selling_price}</span>
                    <span class="book-condition">${book.condition}</span>
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--muted-foreground);">
                    <p>College: ${escapeHtml(book.college_name)}</p>
                    <p>Negotiable: ${book.negotiation}</p>
                </div>
                <div class="action-buttons" style="margin-top: 1rem;">
                    ${book.status === 'sold' ?
                    '<button class="btn btn-outline" disabled>Sold Out</button>' :
                    `<button onclick="showInterest(${book.book_id})" class="btn btn-primary">
                            I'm Interested
                        </button>`
                }
                    <button onclick="viewBookDetails(${book.book_id})" class="btn btn-outline">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    `).join('');
        }
        function viewBookDetails(bookId) {
            window.location.href = `book/book.php?id=${bookId}`;
        }

        function showInterest(bookId) {
            if (!confirm('Express interest in this book? The seller will be notified.')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'express_interest');
            formData.append('book_id', bookId);

            fetch('./server/interested_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Disable the button and update text
                        const interestBtn = document.querySelector(`button[onclick="showInterest(${bookId})"]`);
                        if (interestBtn) {
                            interestBtn.textContent = 'Interest Expressed ✓';
                            interestBtn.disabled = true;
                            interestBtn.classList.remove('btn-primary');
                            interestBtn.classList.add('btn-outline');
                            interestBtn.onclick = null;
                        }
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error expressing interest');
                });
        }

        // Helper function to prevent XSS
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