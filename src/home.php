<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - Student BookSwap</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body onload="loadBooks();">
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">📚 BookSwap</a>
            <div class="nav-menu" id="navMenu">
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
            <div class="filter-group">
                <input type="text" id="searchQuery" placeholder="Search by title, author..." class="form-input">
            </div>

            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <div class="filter-group">
                    <label class="form-label">Max Price (₹)</label>
                    <input type="number" id="priceFilter" placeholder="Max price" class="form-input">
                </div>

                <div class="filter-group">
                    <label class="form-label">Condition</label>
                    <select id="conditionFilter" class="form-select">
                        <option value="">All Conditions</option>
                        <option value="NEW">New</option>
                        <option value="GOOD">Good</option>
                        <option value="FAIR">Fair</option>
                        <option value="POOR">Poor</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="form-label">Negotiable</label>
                    <select id="negotiableFilter" class="form-select">
                        <option value="">All</option>
                        <option value="YES">Yes</option>
                        <option value="NO">No</option>
                    </select>
                </div>
            </div>

            <button onclick="applyFilters()" class="btn btn-primary" style="margin-top: 1rem;">Apply Filters</button>
            <button onclick="clearFilters()" class="btn btn-outline" style="margin-top: 0.5rem;">Clear Filters</button>
        </div>

        <!-- Results Count -->
        <div style="margin-bottom: 1rem;">
            <p id="resultsCount" style="color: var(--muted-foreground);"></p>
        </div>

        <!-- Books Grid -->
        <div id="booksGrid" class="books-grid">
            <!-- Books will be loaded here via AJAX -->
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Student BookSwap. A peer-to-peer book exchange platform for students.</p>
        </div>
    </footer>

    <!-- Book Details Modal -->
    <div id="bookModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

    <script>
        function loadBooks(filters = {}) {
            const formData = new FormData();
            formData.append('action', 'get_books');

            // Add filters to form data
            for (const key in filters) {
                if (filters[key]) {
                    formData.append(key, filters[key]);
                }
            }
            console.log('Loading books with filters:', filters); // Debug log
            fetch('./server/home_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text(); // First get as text to debug
                })
                .then(text => {
                    console.log('Raw response:', text); // Debug log
                    try {
                        const data = JSON.parse(text);
                        console.log('Parsed JSON:', data); // Debug log
                        displayBooks(data.books);
                        document.getElementById('resultsCount').textContent =
                            `Found ${data.books.length} books`;
                    } catch (e) {
                        console.error('JSON parse error:', e, 'Response text:', text);
                        document.getElementById('booksGrid').innerHTML =
                            '<div class="alert alert-error">Error parsing server response</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('booksGrid').innerHTML =
                        '<div class="alert alert-error">Error loading books: ' + error.message + '</div>';
                });
        }
        function displayBooks(books) {
            const booksGrid = document.getElementById('booksGrid');

            if (books.length === 0) {
                booksGrid.innerHTML = '<div class="alert alert-info">No books found matching your criteria.</div>';
                return;
            }

            booksGrid.innerHTML = books.map(book => `
        <div class="book-card">
            <div class="book-image" style="background: var(--muted); display: flex; align-items: center; justify-content: center; color: var(--muted-foreground); height: 200px;">
                📚 Book Image
            </div>
            <div class="book-content">
                <h3 class="book-title">${book.book_name}</h3>
                <p class="book-author">by ${book.first_name} ${book.last_name}</p>
                <p class="book-description" style="color: var(--muted-foreground); font-size: 0.9rem; margin-bottom: 0.5rem;">
                    ${book.descr ? (book.descr.length > 100 ? book.descr.substring(0, 100) + '...' : book.descr) : 'No description available'}
                </p>
                <div class="book-details">
                    <span class="book-price">₹${book.current_selling_price}</span>
                    <span class="book-condition">${book.condition}</span>
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--muted-foreground);">
                    <p>College: ${book.college_name}</p>
                    <p>Negotiable: ${book.negotiation}</p>
                </div>
                <div class="action-buttons" style="margin-top: 1rem;">
                    <button onclick="showInterest(${book.book_id})" class="btn btn-primary">
                        I'm Interested
                    </button>
                    <button onclick="viewBookDetails(${book.book_id})" class="btn btn-outline">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    `).join('');
        }
        function viewBookDetails(bookId) {
            // Redirect to book details page
            window.location.href = `./book/book.php?id=${bookId}`;
        }
    </script>
</body>

</html>