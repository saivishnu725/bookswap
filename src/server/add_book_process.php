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
    $description = $_POST['description'];
    $condition = $_POST['condition'];
    $year_of_purchase = $_POST['year_of_purchase'];
    $cost_at_purchase = $_POST['cost_at_purchase'];
    $current_selling_price = $_POST['current_selling_price'];
    $negotiable = $_POST['negotiable'];

    // deal with image later
    // deal with author names
    // first save the name in author if not exists
    // then get the author_id and save in book_authors


    try {
        $sql = "INSERT INTO books (seller_id, book_name, descr, `condition`, year_of_purchase, cost_at_purchase, current_selling_price, negotiation) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
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
        ]);
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