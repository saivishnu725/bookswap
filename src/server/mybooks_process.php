<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include('connection.php');

$user_id = $_SESSION['user_id'];

$query = "SELECT 
            b.book_id,
            b.seller_id,
            b.book_name,
            b.descr,
            b.`condition`,
            b.year_of_purchase,
            b.cost_at_purchase,
            b.current_selling_price,
            b.negotiation,
            b.status,
            b.author_name,
            COALESCE(bi.image_url, 'assets/images/default-book.png') as image_url
        FROM books b 
        LEFT JOIN book_images bi ON b.book_id = bi.book_id 
        WHERE b.seller_id = ? 
        ORDER BY b.book_id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$books_array = [];
while ($row = $result->fetch_assoc()) {
    $books_array[] = [
        'book_id' => htmlspecialchars($row['book_id']),
        'seller_id' => htmlspecialchars($row['seller_id']),
        'book_name' => htmlspecialchars($row['book_name']),
        'descr' => htmlspecialchars($row['descr']),
        'condition' => htmlspecialchars($row['condition']),
        'year_of_purchase' => htmlspecialchars($row['year_of_purchase']),
        'cost_at_purchase' => htmlspecialchars($row['cost_at_purchase']),
        'current_selling_price' => htmlspecialchars($row['current_selling_price']),
        'negotiation' => htmlspecialchars($row['negotiation']),
        'status' => htmlspecialchars($row['status'] ?? 'available'),
        'author_name' => htmlspecialchars($row['author_name'] ?? ''),
        'image_url' => htmlspecialchars($row['image_url'])
    ];
}

$stmt->close();
?>