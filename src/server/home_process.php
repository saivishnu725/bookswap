<?php

include('connection.php');

// Set headers to prevent caching and ensure JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

        if ($_POST['action'] === 'get_books') {
            // Build base query
            $query = "SELECT 
                        b.book_id,
                        b.book_name,
                        b.author_name,
                        b.descr,
                        b.condition,
                        b.current_selling_price,
                        b.negotiation,
                        b.status,
                        u.college_name,
                        u.first_name,
                        u.last_name
                    FROM books b 
                    JOIN users u ON b.seller_id = u.user_id 
                    WHERE 1=1";

            $params = [];
            $types = '';

            // Apply filters
            if (!empty($_POST['search'])) {
                $query .= " AND (b.book_name LIKE ? OR b.author_name LIKE ?)";
                $searchTerm = '%' . $_POST['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= 'ss';
            }

            if (!empty($_POST['max_price'])) {
                $query .= " AND b.current_selling_price <= ?";
                $params[] = $_POST['max_price'];
                $types .= 'd';
            }

            if (!empty($_POST['condition'])) {
                $query .= " AND b.condition = ?";
                $params[] = $_POST['condition'];
                $types .= 's';
            }

            if (!empty($_POST['negotiable'])) {
                $query .= " AND b.negotiation = ?";
                $params[] = $_POST['negotiable'];
                $types .= 's';
            }

            // Only show available books
            $query .= " AND (b.status IS NULL OR b.status = 'available')";
            $query .= " ORDER BY b.book_id DESC";

            // Prepare statement
            $stmt = $conn->prepare($query);

            if ($params) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }

            $stmt->close();

            echo json_encode([
                'success' => true,
                'books' => $books,
                'count' => count($books)
            ]);

        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }

} catch (Exception $e) {
    // Log the error for debugging
    error_log("Browse process error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}

$conn->close();
?>