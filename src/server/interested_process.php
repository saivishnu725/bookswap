<?php
session_start();


include('connection.php');

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

        if ($_POST['action'] === 'express_interest') {
            $book_id = intval($_POST['book_id']);
            $buyer_id = $_SESSION['user_id'];

            // Check if book exists and get seller info
            $bookQuery = "SELECT seller_id FROM books WHERE book_id = ?";
            $bookStmt = $conn->prepare($bookQuery);
            if (!$bookStmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $bookStmt->bind_param('i', $book_id);
            $bookStmt->execute();
            $bookResult = $bookStmt->get_result();

            if ($bookResult->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Book not found']);
                exit();
            }

            $bookData = $bookResult->fetch_assoc();
            $seller_id = $bookData['seller_id'];
            $bookStmt->close();

            // Check if user is trying to express interest in their own book
            if ($buyer_id == $seller_id) {
                echo json_encode(['success' => false, 'message' => 'You cannot express interest in your own book']);
                exit();
            }

            // Check if already interested
            $checkQuery = "SELECT interest_id FROM book_interests WHERE book_id = ? AND buyer_id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            if (!$checkStmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $checkStmt->bind_param('ii', $book_id, $buyer_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'You have already expressed interest in this book']);
                exit();
            }
            $checkStmt->close();

            // Insert interest
            $insertQuery = "INSERT INTO book_interests (book_id, buyer_id, seller_id, status) VALUES (?, ?, ?, 'requested')";
            $insertStmt = $conn->prepare($insertQuery);
            if (!$insertStmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $insertStmt->bind_param('iii', $book_id, $buyer_id, $seller_id);

            if ($insertStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Interest expressed successfully! The seller will contact you if interested.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to express interest: ' . $insertStmt->error]);
            }

            $insertStmt->close();

        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }

} catch (Exception $e) {
    error_log("Interest process error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

if ($conn) {
    $conn->close();
}
?>