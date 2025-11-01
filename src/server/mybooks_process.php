<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include('connection.php');

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM books WHERE seller_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$user_id]);
/*

1   book_id
2 	seller_id
3 	book_name
4 	descr
5 	condition
6 	year_of_purchase
7 	cost_at_purchase
NEW status
8 	current_selling_price
9 	negotiation
*/
$books_array = [];
$stmt->bind_result(
    $book_id,
    $seller_id,
    $book_name,
    $descr,
    $condition,
    $year_of_purchase,
    $cost_at_purchase,
    $current_selling_price,
    $negotiation,
    $status,
    $author_name,
);
while ($stmt->fetch()) {
    $books_array[] = [
        'book_id' => htmlspecialchars($book_id),
        'seller_id' => htmlspecialchars($seller_id),
        'book_name' => htmlspecialchars($book_name),
        'descr' => htmlspecialchars($descr),
        'status' => htmlspecialchars($status),
        'condition' => htmlspecialchars($condition),
        'year_of_purchase' => htmlspecialchars($year_of_purchase),
        'cost_at_purchase' => htmlspecialchars($cost_at_purchase),
        'current_selling_price' => htmlspecialchars($current_selling_price),
        'negotiation' => htmlspecialchars($negotiation)
    ];
}

?>