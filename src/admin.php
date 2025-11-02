<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student BookSwap</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">📚 BookSwap Admin</a>
            <div class="nav-menu" id="navMenu">
                <a href="index.php" class="nav-link">Main Site</a>
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
    // check for admin
    if ($_SESSION['user_id'] != 1) {
        header("Location: index.php");
        exit();
    }
    ?>

    <div class="page-header">
        <?php echo $_SESSION['user_id'] ?>
        <h1>Admin Dashboard</h1>
        <p>Platform overview and management</p>
    </div>

    <div class="container" style="padding: 2rem 1rem;">
        <!-- Stats Overview -->
        <div class="stats-grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div class="stat-card"
                style="background: var(--card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); text-align: center;">
                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 0.5rem;">👥</div>
                <h3 style="margin-bottom: 0.5rem;">Total Users</h3>
                <div id="totalUsers" style="font-size: 2rem; font-weight: bold; color: var(--primary);">Loading...</div>
            </div>

            <div class="stat-card"
                style="background: var(--card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); text-align: center;">
                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 0.5rem;">📚</div>
                <h3 style="margin-bottom: 0.5rem;">Total Books</h3>
                <div id="totalBooks" style="font-size: 2rem; font-weight: bold; color: var(--primary);">Loading...</div>
            </div>

            <div class="stat-card"
                style="background: var(--card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); text-align: center;">
                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 0.5rem;">💰</div>
                <h3 style="margin-bottom: 0.5rem;">Books Sold</h3>
                <div id="booksSold" style="font-size: 2rem; font-weight: bold; color: var(--primary);">Loading...</div>
            </div>

            <div class="stat-card"
                style="background: var(--card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); text-align: center;">
                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 0.5rem;">🤝</div>
                <h3 style="margin-bottom: 0.5rem;">Active Interests</h3>
                <div id="activeInterests" style="font-size: 2rem; font-weight: bold; color: var(--primary);">Loading...
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div style="margin-bottom: 3rem;">
            <h2 style="margin-bottom: 1.5rem;">Quick Actions</h2>
            <div class="action-buttons">
                <a href="admin_users.php" class="btn btn-primary">Manage Users</a>
                <a href="admin_books.php" class="btn btn-primary">Manage Books</a>
                <a href="admin_transactions.php" class="btn btn-primary">View Transactions</a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div>
            <h2 style="margin-bottom: 1.5rem;">Recent Activity</h2>
            <div id="recentActivity"
                style="background: var(--card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border);">
                <div class="alert alert-info">Loading recent activity...</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Student BookSwap. Admin Dashboard</p>
        </div>
    </footer>

    <script>
        // Load dashboard stats
        document.addEventListener('DOMContentLoaded', function () {
            loadDashboardStats();
            loadRecentActivity();
        });

        function loadDashboardStats() {
            fetch('server/admin_process.php?action=get_stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('totalUsers').textContent = data.stats.total_users;
                        document.getElementById('totalBooks').textContent = data.stats.total_books;
                        document.getElementById('booksSold').textContent = data.stats.books_sold;
                        document.getElementById('activeInterests').textContent = data.stats.active_interests;
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }

        function loadRecentActivity() {
            fetch('server/admin_process.php?action=get_recent_activity')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRecentActivity(data.activity);
                    }
                })
                .catch(error => {
                    console.error('Error loading activity:', error);
                });
        }

        function displayRecentActivity(activity) {
            const container = document.getElementById('recentActivity');

            if (activity.length === 0) {
                container.innerHTML = '<div class="alert alert-info">No recent activity</div>';
                return;
            }

            container.innerHTML = activity.map(item => `
                <div style="padding: 1rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>${item.action}</strong>
                        <p style="margin: 0.25rem 0 0 0; color: var(--muted-foreground); font-size: 0.9rem;">
                            ${item.details}
                        </p>
                    </div>
                    <div style="text-align: right; color: var(--muted-foreground); font-size: 0.875rem;">
                        ${new Date(item.timestamp).toLocaleString()}
                    </div>
                </div>
            `).join('');
        }
    </script>
</body>

</html>