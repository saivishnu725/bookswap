<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details - Student BookSwap</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">📚 BookSwap</a>
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
        header("Location: index.php");
        exit();
    }
    ?>

    <div class="container" style="padding: 2rem 1rem;">
        <div id="bookDetailsContainer">
            <!-- Book details will be loaded here via AJAX -->
            <div class="alert alert-info">Loading book details...</div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Student BookSwap. A peer-to-peer book exchange platform for students.</p>
        </div>
    </footer>

    <script>
        // Get book ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const bookId = urlParams.get('id');
        console.log('Book ID from URL:', bookId); // Debug log

        if (bookId) {
            loadBookDetails(bookId);
        } else {
            document.getElementById('bookDetailsContainer').innerHTML =
                '<div class="alert alert-error">Book ID not specified</div>';
        }

        function loadBookDetails(bookId) {
            const formData = new FormData();
            formData.append('action', 'get_book_details');
            formData.append('book_id', bookId);
            console.log('form: ', formData); // Debug log
            fetch('../server/book_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Book details response:', data.book); // Debug log
                        displayBookDetails(data.book);
                    } else {
                        console.log('Book details response:', data.message); // Debug log
                        document.getElementById('bookDetailsContainer').innerHTML =
                            `<div class="alert alert-error">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('bookDetailsContainer').innerHTML =
                        '<div class="alert alert-error">Error loading book details</div>';
                });
        }

        function displayBookDetails(book) {
            document.getElementById('bookDetailsContainer').innerHTML = `
                <div style="max-width: 800px; margin: 0 auto;">
                    <a href="../home.php" class="btn btn-outline" style="margin-bottom: 2rem;">← Back to Browse</a>
                    
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; margin-bottom: 2rem;">
                        <!-- Book Image -->
                        <div>
                            <div class="book-image" style="background: var(--muted); display: flex; align-items: center; justify-content: center; color: var(--muted-foreground); height: 300px; border-radius: var(--radius);">
                                📚 Book Image
                            </div>
                        </div>
                        
                        <!-- Book Info -->
                        <div>
                            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">${book.book_name}</h1>
                            <p style="font-size: 1.2rem; color: var(--muted-foreground); margin-bottom: 1.5rem;">
                                Listed by ${book.first_name} ${book.last_name} from ${book.college_name}
                            </p>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                                <div style="background: var(--muted); padding: 1rem; border-radius: var(--radius);">
                                    <strong>Price</strong>
                                    <div style="font-size: 2rem; color: var(--primary);">₹${book.current_selling_price}</div>
                                </div>
                                <div style="background: var(--muted); padding: 1rem; border-radius: var(--radius);">
                                    <strong>Condition</strong>
                                    <div style="font-size: 1.2rem;">${book.condition}</div>
                                </div>
                                <div style="background: var(--muted); padding: 1rem; border-radius: var(--radius);">
                                    <strong>Negotiable</strong>
                                    <div style="font-size: 1.2rem;">${book.negotiation}</div>
                                </div>
                            </div>
                            
                            <div class="action-buttons">
                                <button onclick="showInterest(${book.book_id})" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.2rem;">
                                    I'm Interested
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Book Description -->
                    <div style="background: var(--card); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--border);">
                        <h2 style="margin-bottom: 1rem;">Description</h2>
                        <p style="line-height: 1.6; color: var(--foreground);">${book.descr || 'No description provided.'}</p>
                    </div>
                    
                    <!-- Additional Details -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                        <div style="background: var(--muted); padding: 1.5rem; border-radius: var(--radius);">
                            <h3 style="margin-bottom: 1rem;">Purchase Details</h3>
                            <p><strong>Year of Purchase:</strong> ${book.year_of_purchase || 'Not specified'}</p>
                            <p><strong>Original Cost:</strong> ₹${book.cost_at_purchase || 'Not specified'}</p>
                        </div>
                        
                        <div style="background: var(--muted); padding: 1.5rem; border-radius: var(--radius);">
                            <h3 style="margin-bottom: 1rem;">Seller Information</h3>
                            <p><strong>Name:</strong> ${book.first_name} ${book.last_name}</p>
                            <p><strong>College:</strong> ${book.college_name}</p>
                        </div>
                    </div>
                </div>
            `;
        }

        function showInterest(bookId) {
            if (!confirm('Express interest in this book? The seller will be notified.')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'express_interest');
            formData.append('book_id', bookId);

            fetch('../server/interested_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(text => {
                    console.log('Raw interest response:', text); // Debug log
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            alert(data.message);
                            // Optionally disable the button after successful interest
                            const interestBtn = document.querySelector(`button[onclick="showInterest(${bookId})"]`);
                            if (interestBtn) {
                                interestBtn.textContent = 'Interest Expressed ✓';
                                interestBtn.disabled = true;
                                interestBtn.classList.remove('btn-primary');
                                interestBtn.classList.add('btn-outline');
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    } catch (e) {
                        console.error('JSON parse error:', e, 'Response text:', text);
                        alert('Error parsing server response');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error expressing interest: ' + error.message);
                });
        }
    </script>
</body>

</html>