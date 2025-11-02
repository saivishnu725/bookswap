<?php

include('connection.php');

header('Content-Type: application/json');

try {
    if (isset($_GET['action'])) {

        if ($_GET['action'] === 'get_stats') {
            // Get total users
            $users_sql = "SELECT COUNT(*) as total FROM users";
            $users_result = $conn->query($users_sql);
            $total_users = $users_result->fetch_assoc()['total'];

            // Get total books
            $books_sql = "SELECT COUNT(*) as total FROM books";
            $books_result = $conn->query($books_sql);
            $total_books = $books_result->fetch_assoc()['total'];

            // Get books sold
            $sold_sql = "SELECT COUNT(*) as total FROM books WHERE status = 'sold'";
            $sold_result = $conn->query($sold_sql);
            $books_sold = $sold_result->fetch_assoc()['total'];

            // Get active interests
            $interests_sql = "SELECT COUNT(*) as total FROM book_interests WHERE status = 'requested' OR status = 'approved'";
            $interests_result = $conn->query($interests_sql);
            $active_interests = $interests_result->fetch_assoc()['total'];

            echo json_encode([
                'success' => true,
                'stats' => [
                    'total_users' => $total_users,
                    'total_books' => $total_books,
                    'books_sold' => $books_sold,
                    'active_interests' => $active_interests
                ]
            ]);

        } elseif ($_GET['action'] === 'get_recent_activity') {
            // Get recent book listings
            $books_sql = "SELECT book_name, created_at FROM books ORDER BY book_id DESC LIMIT 5";
            $books_result = $conn->query($books_sql);

            // Get recent interests
            $interests_sql = "SELECT bi.status, b.book_name, u.first_name, u.last_name, bi.interest_date 
                            FROM book_interests bi 
                            JOIN books b ON bi.book_id = b.book_id 
                            JOIN users u ON bi.buyer_id = u.user_id 
                            ORDER BY bi.interest_date DESC LIMIT 5";
            $interests_result = $conn->query($interests_sql);

            $activity = [];

            // Add recent book listings to activity
            while ($book = $books_result->fetch_assoc()) {
                $activity[] = [
                    'action' => 'New Book Listed',
                    'details' => $book['book_name'],
                    'timestamp' => $book['created_at']
                ];
            }

            // Add recent interests to activity
            while ($interest = $interests_result->fetch_assoc()) {
                $activity[] = [
                    'action' => 'Interest ' . ucfirst($interest['status']),
                    'details' => $interest['first_name'] . ' ' . $interest['last_name'] . ' in ' . $interest['book_name'],
                    'timestamp' => $interest['interest_date']
                ];
            }

            // Sort by timestamp
            usort($activity, function ($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });

            // Take only latest 5
            $activity = array_slice($activity, 0, 5);

            echo json_encode([
                'success' => true,
                'activity' => $activity
            ]);

        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'No action specified']);
    }

} catch (Exception $e) {
    error_log("Admin process error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

$conn->close();
?>