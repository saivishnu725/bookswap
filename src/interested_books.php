<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Interests - Student BookSwap</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">📚 BookSwap</a>
            <div class="nav-menu" id="navMenu">
                <a href="index.php" class="nav-link">Home</a>
                <a href="browse.php" class="nav-link">Browse</a>
                <a href="mybooks.php" class="nav-link">My Books</a>
                <a href="interested_books.php" class="nav-link">Interests</a>
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
        <h1>My Interests</h1>
        <p>Manage your book interests and requests</p>
    </div>

    <div class="container" style="padding: 2rem 1rem;">
        <!-- Section A: People interested in my books -->
        <div class="interests-section" style="margin-bottom: 3rem;">
            <h2 style="margin-bottom: 1.5rem;">📥 People Interested in My Books</h2>
            <div id="incomingInterests">
                <div class="alert alert-info">Loading incoming interests...</div>
            </div>
        </div>

        <!-- Section B: Books I'm interested in -->
        <div class="interests-section">
            <h2 style="margin-bottom: 1.5rem;">📤 Books I'm Interested In</h2>
            <div id="outgoingInterests">
                <div class="alert alert-info">Loading your interests...</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Student BookSwap. A peer-to-peer book exchange platform for students.</p>
        </div>
    </footer>
    <!-- Seller Contact Modal -->
    <div id="sellerContactModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <span class="modal-close" onclick="closeSellerContactModal()">&times;</span>
            <h2 style="margin-bottom: 1rem;">Seller Contact Information</h2>
            <div id="sellerContactContent" style="line-height: 1.6;">
                <!-- Contact info will be loaded here -->
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button onclick="closeSellerContactModal()" class="btn btn-primary">Close</button>
            </div>
        </div>
    </div>
    <script>
        // Load both sections when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadIncomingInterests();
            loadOutgoingInterests();
        });

        function loadIncomingInterests() {
            const formData = new FormData();
            formData.append('action', 'get_incoming_interests');

            fetch('server/interested_books_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayIncomingInterests(data.interests);
                    } else {
                        document.getElementById('incomingInterests').innerHTML =
                            `<div class="alert alert-error">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('incomingInterests').innerHTML =
                        '<div class="alert alert-error">Error loading incoming interests</div>';
                });
        }

        function loadOutgoingInterests() {
            const formData = new FormData();
            formData.append('action', 'get_outgoing_interests');

            fetch('server/interested_books_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayOutgoingInterests(data.interests);
                    } else {
                        document.getElementById('outgoingInterests').innerHTML =
                            `<div class="alert alert-error">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('outgoingInterests').innerHTML =
                        '<div class="alert alert-error">Error loading your interests</div>';
                });
        }

        function displayIncomingInterests(interests) {
            const container = document.getElementById('incomingInterests');

            if (interests.length === 0) {
                container.innerHTML = '<div class="alert alert-info">No one has expressed interest in your books yet.</div>';
                return;
            }

            container.innerHTML = interests.map(interest => `
                <div class="book-card" style="margin-bottom: 1.5rem;">
                    <div class="book-content">
                        <div style="display: flex; justify-content: between; align-items: start; gap: 1rem;">
                            <div style="flex: 1;">
                                <h3 class="book-title">${escapeHtml(interest.book_name)}</h3>
                                <p style="color: var(--muted-foreground); margin-bottom: 1rem;">
                                    Interested Buyer: <strong>${escapeHtml(interest.buyer_name)}</strong> from ${escapeHtml(interest.buyer_college)}
                                </p>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                                    <div>
                                        <strong>Interest Date:</strong><br>
                                        ${new Date(interest.interest_date).toLocaleDateString()}
                                    </div>
                                    <div>
                                        <strong>Status:</strong><br>
                                        <span class="book-condition">${interest.status.toUpperCase()}</span>
                                    </div>
                                </div>
                            </div>
                            <div style="min-width: 200px;">
                                ${getIncomingInterestActions(interest)}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function displayOutgoingInterests(interests) {
            const container = document.getElementById('outgoingInterests');

            if (interests.length === 0) {
                container.innerHTML = '<div class="alert alert-info">You haven\'t expressed interest in any books yet.</div>';
                return;
            }

            container.innerHTML = interests.map(interest => `
                <div class="book-card" style="margin-bottom: 1.5rem;">
                    <div class="book-content">
                        <div style="display: flex; justify-content: between; align-items: start; gap: 1rem;">
                            <div style="flex: 1;">
                                <h3 class="book-title">${escapeHtml(interest.book_name)}</h3>
                                <p style="color: var(--muted-foreground); margin-bottom: 0.5rem;">
                                    Seller: ${escapeHtml(interest.seller_name)} from ${escapeHtml(interest.seller_college)}
                                </p>
                                <p class="book-price" style="font-size: 1.25rem; margin-bottom: 0.5rem;">₹${interest.book_price}</p>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                                    <div>
                                        <strong>Interest Date:</strong><br>
                                        ${new Date(interest.interest_date).toLocaleDateString()}
                                    </div>
                                    <div>
                                        <strong>Status:</strong><br>
                                        <span class="book-condition">${interest.status.toUpperCase()}</span>
                                    </div>
                                </div>
                            </div>
                            <div style="min-width: 200px;">
                                ${getOutgoingInterestActions(interest)}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getIncomingInterestActions(interest) {
            switch (interest.status) {
                case 'requested':
                    return `
                        <div class="action-buttons">
                            <button onclick="approveInterest(${interest.interest_id})" class="btn btn-primary">
                                Approve & Share Contact
                            </button>
                            <button onclick="rejectInterest(${interest.interest_id})" class="btn btn-outline">
                                Reject
                            </button>
                        </div>
                    `;
                case 'approved':
                    return `
                        <div>
                            <p style="color: var(--primary); font-weight: bold; margin-bottom: 0.5rem;">✅ Contact Shared</p>
                            <div style="background: var(--muted); padding: 0.75rem; border-radius: var(--radius); font-size: 0.9rem;">
                                <strong>Buyer Contact:</strong><br>
                                ${escapeHtml(interest.buyer_name)}<br>
                                ${escapeHtml(interest.buyer_email)}<br>
                                ${escapeHtml(interest.buyer_phone)}
                            </div>
                            <div class="action-buttons" style="margin-top: 0.5rem;">
                                <button onclick="markAsSold(${interest.interest_id}, ${interest.book_id})" class="btn btn-primary">
                                    Mark as Sold
                                </button>
                            </div>
                        </div>
                    `;
                case 'rejected':
                    return `<p style="color: #dc2626;">❌ Interest Rejected</p>`;
                case 'sold':
                    return `<p style="color: #16a34a;">✅ Sold to this buyer</p>`;
                default:
                    return `<p>Status: ${interest.status}</p>`;
            }
        }

        function getOutgoingInterestActions(interest) {
            switch (interest.status) {
                case 'requested':
                    return `<p style="color: var(--muted-foreground);">⏳ Interest Expressed - Waiting for seller approval</p>`;
                case 'approved':
                    return `
                <div>
                    <p style="color: var(--primary); font-weight: bold; margin-bottom: 0.5rem;">✅ Seller Approved!</p>
                    <button onclick="showSellerContact(${interest.interest_id})" class="btn btn-primary">
                        View Seller Contact
                    </button>
                </div>
            `;
                case 'rejected':
                    return `<p style="color: #dc2626;">❌ Seller Rejected Your Interest</p>`;
                case 'sold':
                    // For outgoing interests, the current user is always the buyer, so show "Purchased"
                    return `<p style="color: #16a34a; font-weight: bold;">✅ Purchased</p>`;
                default:
                    return `<p>Status: ${interest.status}</p>`;
            }
        }
        function approveInterest(interestId) {
            if (!confirm('Approve this interest and share your contact information?')) return;

            updateInterestStatus(interestId, 'approved');
        }

        function rejectInterest(interestId) {
            if (!confirm('Reject this interest request?')) return;

            updateInterestStatus(interestId, 'rejected');
        }

        function markAsSold(interestId, bookId) {
            if (!confirm('Mark this book as sold to this buyer? This will notify all other interested buyers.')) return;

            updateInterestStatus(interestId, 'sold', bookId);
        }

        function updateInterestStatus(interestId, status, bookId = null) {
            const formData = new FormData();
            formData.append('action', 'update_interest_status');
            formData.append('interest_id', interestId);
            formData.append('status', status);
            if (bookId) formData.append('book_id', bookId);

            fetch('./server/interested_books_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        loadIncomingInterests();
                        loadOutgoingInterests();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating interest status');
                });
        }

        function showSellerContact(interestId) {
            const formData = new FormData();
            formData.append('action', 'get_seller_contact');
            formData.append('interest_id', interestId);

            fetch('server/interested_books_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displaySellerContactModal(data.seller);
                    } else {
                        showErrorModal('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorModal('Error getting seller contact information');
                });
        }

        function displaySellerContactModal(seller) {
            const modal = document.getElementById('sellerContactModal');
            const content = document.getElementById('sellerContactContent');

            content.innerHTML = `
        <div style="background: var(--muted); padding: 1.5rem; border-radius: var(--radius);">
            <div style="display: grid; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.2rem;">👤</span>
                    <div>
                        <strong>Name</strong><br>
                        ${escapeHtml(seller.name)}
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.2rem;">📧</span>
                    <div>
                        <strong>Email</strong><br>
                        <a href="mailto:${escapeHtml(seller.email)}" style="color: var(--primary);">
                            ${escapeHtml(seller.email)}
                        </a>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.2rem;">📞</span>
                    <div>
                        <strong>Phone</strong><br>
                        <a href="tel:${escapeHtml(seller.phone)}" style="color: var(--primary);">
                            ${escapeHtml(seller.phone)}
                        </a>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.2rem;">🏫</span>
                    <div>
                        <strong>College</strong><br>
                        ${escapeHtml(seller.college)}
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 1.5rem; padding: 1rem; background: #dbeafe; border-radius: var(--radius); border-left: 4px solid var(--primary);">
            <strong>💡 Contact the seller</strong>
            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #1e40af;">
                Reach out to discuss the book details, pricing, and meeting arrangements.
            </p>
        </div>
    `;

            modal.style.display = 'block';
        }

        function closeSellerContactModal() {
            document.getElementById('sellerContactModal').style.display = 'none';
        }

        function showErrorModal(message) {
            const modal = document.getElementById('sellerContactModal');
            const content = document.getElementById('sellerContactContent');

            content.innerHTML = `
        <div class="alert alert-error">
            ${escapeHtml(message)}
        </div>
    `;

            modal.style.display = 'block';
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('sellerContactModal');
            if (event.target == modal) {
                closeSellerContactModal();
            }
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