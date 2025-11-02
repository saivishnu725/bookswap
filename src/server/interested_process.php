<?php
session_start();

// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log session info
error_log("Interest Process - Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated - please log in again']);
    exit();
}

include('connection.php');

// Check connection
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

        if ($_POST['action'] === 'express_interest') {
            $book_id = intval($_POST['book_id']);
            $buyer_id = intval($_SESSION['user_id']);

            error_log("Express Interest - Book ID: $book_id, Buyer ID: $buyer_id");

            // Step 1: Verify buyer exists
            $buyer_check_sql = "SELECT user_id FROM users WHERE user_id = ?";
            $buyer_stmt = $conn->prepare($buyer_check_sql);
            if (!$buyer_stmt) {
                throw new Exception("Buyer check prepare failed: " . $conn->error);
            }

            $buyer_stmt->bind_param('i', $buyer_id);
            $buyer_stmt->execute();
            $buyer_result = $buyer_stmt->get_result();

            if ($buyer_result->num_rows === 0) {
                $buyer_stmt->close();
                error_log("Buyer ID $buyer_id not found in users table");
                echo json_encode(['success' => false, 'message' => 'Your account was not found. Please log in again.']);
                exit();
            }
            $buyer_stmt->close();
            error_log("Buyer verification passed");

            // Step 2: Get book and seller info
            $book_sql = "SELECT book_id, seller_id, book_name FROM books WHERE book_id = ?";
            $book_stmt = $conn->prepare($book_sql);
            if (!$book_stmt) {
                throw new Exception("Book check prepare failed: " . $conn->error);
            }

            $book_stmt->bind_param('i', $book_id);
            $book_stmt->execute();
            $book_result = $book_stmt->get_result();

            if ($book_result->num_rows === 0) {
                $book_stmt->close();
                error_log("Book ID $book_id not found");
                echo json_encode(['success' => false, 'message' => 'Book not found']);
                exit();
            }

            $book_data = $book_result->fetch_assoc();
            $seller_id = $book_data['seller_id'];
            $book_name = $book_data['book_name'];
            $book_stmt->close();
            error_log("Book found: $book_name, Seller ID: $seller_id");

            // Step 3: Check if user is trying to buy their own book
            if ($buyer_id === $seller_id) {
                error_log("User tried to express interest in their own book");
                echo json_encode(['success' => false, 'message' => 'You cannot express interest in your own book']);
                exit();
            }

            // Step 4: Check if already interested (using simple query first)
            $check_sql = "SELECT interest_id FROM book_interests WHERE book_id = ? AND buyer_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            if (!$check_stmt) {
                throw new Exception("Interest check prepare failed: " . $conn->error);
            }

            $check_stmt->bind_param('ii', $book_id, $buyer_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $check_stmt->close();
                error_log("User already expressed interest in this book");
                echo json_encode(['success' => false, 'message' => 'You have already expressed interest in this book']);
                exit();
            }
            $check_stmt->close();
            error_log("No duplicate interest found");

            // Step 5: Insert the interest
            $insert_sql = "INSERT INTO book_interests (book_id, buyer_id, seller_id, status, interest_date) VALUES (?, ?, ?, 'requested', NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            if (!$insert_stmt) {
                throw new Exception("Insert prepare failed: " . $conn->error);
            }

            $insert_stmt->bind_param('iii', $book_id, $buyer_id, $seller_id);

            if ($insert_stmt->execute()) {
                $inserted_id = $insert_stmt->insert_id;
                $insert_stmt->close();

                error_log("Interest successfully recorded with ID: $inserted_id");
                echo json_encode([
                    'success' => true,
                    'message' => 'Interest expressed successfully! The seller will contact you if interested.',
                    'interest_id' => $inserted_id
                ]);
            } else {
                $error = $insert_stmt->error;
                $insert_stmt->close();
                error_log("Insert failed: " . $error);
                echo json_encode(['success' => false, 'message' => 'Failed to express interest: ' . $error]);
            }

        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $_POST['action']]);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method or missing action']);
    }

} catch (Exception $e) {
    error_log("Interest process exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

if ($conn) {
    $conn->close();
}
?>