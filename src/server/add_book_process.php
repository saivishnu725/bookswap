<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('connection.php');

    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $condition = $_POST['condition'];
    $year_of_purchase = $_POST['year_of_purchase'];
    $cost_at_purchase = $_POST['cost_at_purchase'];
    $current_selling_price = $_POST['current_selling_price'];
    $negotiable = $_POST['negotiable'];

    try {
        // Insert book first
        $sql = "INSERT INTO books (seller_id, book_name, descr, `condition`, year_of_purchase, cost_at_purchase, current_selling_price, negotiation, author_name) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $user_id,
            $title,
            $description,
            $condition,
            $year_of_purchase,
            $cost_at_purchase,
            $current_selling_price,
            $negotiable,
            $author
        ]);

        $book_id = $conn->insert_id;

        // Handle image upload
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
                    // Save to database
                    $image_url = 'assets/images/books/' . $filename;
                    $image_sql = "INSERT INTO book_images (book_id, image_url) VALUES (?, ?)";
                    $image_stmt = $conn->prepare($image_sql);
                    $image_stmt->execute([$book_id, $image_url]);
                    $image_stmt->close();
                }
            }
        }

        header("Location: ../home.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../book/add_book.php");
    exit();
}
?>