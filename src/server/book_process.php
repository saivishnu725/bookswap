<?php

include('connection.php');

// Check connection
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

        if ($_POST['action'] === 'get_book_details') {
            $book_id = intval($_POST['book_id']);

            $query = "SELECT 
                    b.*,
                    u.college_name,
                    u.first_name,
                    u.last_name,
                    u.phone_primary,
                    u.email,
                    COALESCE(bi.image_url, 'assets/images/default-book.png') as image_url
                FROM books b 
                JOIN users u ON b.seller_id = u.user_id 
                LEFT JOIN book_images bi ON b.book_id = bi.book_id 
                WHERE b.book_id = ?";

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param('i', $book_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Book not found']);
                exit();
            }

            $book = $result->fetch_assoc();
            $stmt->close();

            echo json_encode([
                'success' => true,
                'book' => $book
            ]);

        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }

} catch (Exception $e) {
    error_log("Book process error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

if ($conn) {
    $conn->close();
}
?>