<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include('connection.php');

// Handle POST request - update book data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']);
    $user_id = $_SESSION['user_id'];

    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $condition = $_POST['condition'];
    $year_of_purchase = $_POST['year_of_purchase'];
    $cost_at_purchase = $_POST['cost_at_purchase'];
    $current_selling_price = $_POST['current_selling_price'];
    $negotiable = $_POST['negotiable'];
    $status = $_POST['status'];

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
            $_SESSION['error'] = "You are not authorized to edit this book";
            header("Location: ../mybooks.php");
            exit();
        }
        $verify_stmt->close();

        // Update book data
        $update_sql = "UPDATE books SET 
                        book_name = ?, 
                        author_name = ?,
                        descr = ?, 
                        `condition` = ?, 
                        year_of_purchase = ?, 
                        cost_at_purchase = ?, 
                        current_selling_price = ?, 
                        negotiation = ?,
                        status = ?
                      WHERE book_id = ?";

        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param(
            'ssssiidssi',
            $title,
            $author,
            $description,
            $condition,
            $year_of_purchase,
            $cost_at_purchase,
            $current_selling_price,
            $negotiable,
            $status,
            $book_id
        );

        if ($update_stmt->execute()) {
            // Handle image upload if provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $_FILES['image'];

                // Validate image
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB

                if (in_array($image['type'], $allowed_types) && $image['size'] <= $max_size) {

                    // Create uploads directory if it doesn't exist
                    $upload_dir = '../assets/images/books/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    // Generate unique filename
                    $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                    $filename = 'book_' . $book_id . '_' . time() . '.' . $file_extension;
                    $file_path = $upload_dir . $filename;

                    // Move uploaded file
                    if (move_uploaded_file($image['tmp_name'], $file_path)) {
                        // Delete old image if exists
                        $delete_old_sql = "DELETE FROM book_images WHERE book_id = ?";
                        $delete_stmt = $conn->prepare($delete_old_sql);
                        $delete_stmt->bind_param('i', $book_id);
                        $delete_stmt->execute();
                        $delete_stmt->close();

                        // Save new image
                        $image_url = 'assets/images/books/' . $filename;
                        $image_sql = "INSERT INTO book_images (book_id, image_url) VALUES (?, ?)";
                        $image_stmt = $conn->prepare($image_sql);
                        $image_stmt->execute([$book_id, $image_url]);
                        $image_stmt->close();
                    }
                }
            }

            $_SESSION['success'] = "Book updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update book";
        }

        $update_stmt->close();

    } catch (Exception $e) {
        $_SESSION['error'] = "Server error: " . $e->getMessage();
    }

    header("Location: ../mybooks.php");
    exit();
} else {
    // If not a POST request, redirect to mybooks
    header("Location: ../mybooks.php");
    exit();
}
?>