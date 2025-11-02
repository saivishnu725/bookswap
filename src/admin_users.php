<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Student BookSwap Admin</title>
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
                <!-- <a href="admin_transactions.php" class="nav-link">Transactions</a> -->
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
        <h1>Manage Users</h1>
        <p>View and manage all registered users</p>
    </div>

    <div class="container" style="padding: 2rem 1rem;">
        <!-- Search and Filters -->
        <div class="filters">
            <div class="filter-group">
                <input type="text" id="searchQuery" placeholder="Search by name, email, college..." class="form-input"
                    onkeyup="debounceSearch()">
            </div>

            <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                <button onclick="loadUsers()" class="btn btn-primary">Search</button>
                <button onclick="clearSearch()" class="btn btn-outline">Clear</button>
            </div>
        </div>

        <!-- Results Count -->
        <div style="margin: 1rem 0;">
            <p id="resultsCount" style="color: var(--muted-foreground);">Loading users...</p>
        </div>

        <!-- Users Table -->
        <div id="usersTable" style="background: var(--card); border-radius: var(--radius); overflow: hidden;">
            <div class="alert alert-info">Loading users...</div>
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
            loadUsers();
        });

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadUsers();
            }, 500);
        }

        function loadUsers() {
            const search = document.getElementById('searchQuery').value;

            fetch(`server/admin_users_process.php?action=get_users&search=${encodeURIComponent(search)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayUsers(data.users);
                        document.getElementById('resultsCount').textContent =
                            `Found ${data.users.length} users`;
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('usersTable').innerHTML =
                        `<div class="alert alert-error">Error loading users: ${error.message}</div>`;
                });
        }

        function clearSearch() {
            document.getElementById('searchQuery').value = '';
            loadUsers();
        }

        function displayUsers(users) {
            const container = document.getElementById('usersTable');

            if (users.length === 0) {
                container.innerHTML = '<div class="alert alert-info">No users found</div>';
                return;
            }

            container.innerHTML = `
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: var(--muted);">
                            <tr>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">User ID</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Name</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Email</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">College</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Phone</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Trust Score</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Joined</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid var(--border);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${users.map(user => `
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 1rem;">${user.user_id}</td>
                                    <td style="padding: 1rem;">
                                        <strong>${escapeHtml(user.first_name)} ${escapeHtml(user.last_name)}</strong>
                                    </td>
                                    <td style="padding: 1rem;">${escapeHtml(user.email)}</td>
                                    <td style="padding: 1rem;">${escapeHtml(user.college_name || 'N/A')}</td>
                                    <td style="padding: 1rem;">${escapeHtml(user.phone_primary)}</td>
                                    <td style="padding: 1rem;">
                                        <span class="book-condition">${user.trust_score}/10</span>
                                    </td>
                                    <td style="padding: 1rem;">${new Date(user.created_at).toLocaleDateString()}</td>
                                    <td style="padding: 1rem;">
                                        <button onclick="viewUserDetails(${user.user_id})" class="btn btn-outline" style="padding: 0.5rem 1rem;">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function viewUserDetails(userId) {
            alert('User details view for ID: ' + userId + ' will be implemented');
            // Could open a modal or redirect to user details page
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