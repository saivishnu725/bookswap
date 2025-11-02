<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $book_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    try {
        // Verify user owns this book
        $verify_sql = "SELECT seller_id FROM books WHERE book_id = ?";
        $verify_stmt = $conn->prepare($verify_sql);
        $verify_stmt->bind_param('i', $book_id);
        $verify_stmt->execute();
        $verify_result = $verify_stmt->get_result();

        if ($verify_result->num_rows === 0) {
            $_SESSION['error'] = "Book not found";
            header("Location: ../mybooks.php");
            exit();
        }

        $book_data = $verify_result->fetch_assoc();
        if ($book_data['seller_id'] != $user_id) {
            $_SESSION['error'] = "You are not authorized to delete this book";
            header("Location: ../mybooks.php");
            exit();
        }
        $verify_stmt->close();

        // Delete the book (cascade will handle book_images and book_interests)
        $delete_sql = "DELETE FROM books WHERE book_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param('i', $book_id);

        if ($delete_stmt->execute()) {
            $_SESSION['success'] = "Book deleted successfully";
        } else {
            $_SESSION['error'] = "Failed to delete book";
        }

        $delete_stmt->close();

    } catch (Exception $e) {
        $_SESSION['error'] = "Server error: " . $e->getMessage();
    }

    header("Location: ../mybooks.php");
    exit();
} else {
    header("Location: ../mybooks.php");
    exit();
}
?>