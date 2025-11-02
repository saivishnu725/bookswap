<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

include('connection.php');

header('Content-Type: application/json');

try {
    if (isset($_GET['action'])) {

        if ($_GET['action'] === 'get_books') {
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';
            $condition = $_GET['condition'] ?? '';

            $query = "SELECT 
                        b.book_id, b.book_name, b.author_name, b.current_selling_price,
                        b.condition, b.status,
                        CONCAT(u.first_name, ' ', u.last_name) as seller_name,
                        u.college_name as seller_college
                    FROM books b 
                    JOIN users u ON b.seller_id = u.user_id 
                    WHERE 1=1";

            $params = [];
            $types = '';

            if (!empty($search)) {
                $query .= " AND (b.book_name LIKE ? OR b.author_name LIKE ?)";
                $searchTerm = '%' . $search . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= 'ss';
            }

            if (!empty($status)) {
                $query .= " AND b.status = ?";
                $params[] = $status;
                $types .= 's';
            }

            if (!empty($condition)) {
                $query .= " AND b.condition = ?";
                $params[] = $condition;
                $types .= 's';
            }

            $query .= " ORDER BY b.book_id DESC";

            if ($params) {
                $stmt = $conn->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $conn->query($query);
            }

            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }

            if (isset($stmt))
                $stmt->close();

            echo json_encode([
                'success' => true,
                'books' => $books
            ]);

        } elseif ($_GET['action'] === 'delete_book') {
            $book_id = intval($_GET['book_id']);

            // Delete the book (cascade will handle images and interests)
            $delete_sql = "DELETE FROM books WHERE book_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param('i', $book_id);

            if ($delete_stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Book deleted successfully']);
            } else {
                throw new Exception("Failed to delete book");
            }

            $delete_stmt->close();

        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'No action specified']);
    }

} catch (Exception $e) {
    error_log("Admin books process error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error' . $e->getMessage()]);
}

$conn->close();
?>