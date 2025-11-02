<?php
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header('Content-Type: application/json');
//     echo json_encode(['success' => false, 'message' => 'Not authenticated']);
//     exit();
// }

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('connection.php');

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
                    WHERE (b.status IS NULL OR b.status = 'available')
                    AND b.seller_id != " . intval($_SESSION['user_id']);

            $params = [];
            $types = '';

            // Search filter
            if (!empty($_POST['search'])) {
                $query .= " AND (b.book_name LIKE ? OR b.author_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
                $searchTerm = '%' . $_POST['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= 'ssss';
            }

            // Max price filter
            if (!empty($_POST['max_price'])) {
                $query .= " AND b.current_selling_price <= ?";
                $params[] = $_POST['max_price'];
                $types .= 'd';
            }

            // Condition filter
            if (!empty($_POST['condition'])) {
                $query .= " AND b.condition = ?";
                $params[] = $_POST['condition'];
                $types .= 's';
            }

            // Negotiable filter
            if (!empty($_POST['negotiable'])) {
                $query .= " AND b.negotiation = ?";
                $params[] = $_POST['negotiable'];
                $types .= 's';
            }

            // Sorting
            $sort = $_POST['sort'] ?? 'newest';
            switch ($sort) {
                case 'price_low':
                    $query .= " ORDER BY b.current_selling_price ASC";
                    break;
                case 'price_high':
                    $query .= " ORDER BY b.current_selling_price DESC";
                    break;
                case 'name':
                    $query .= " ORDER BY b.book_name ASC";
                    break;
                case 'newest':
                default:
                    $query .= " ORDER BY b.book_id DESC";
                    break;
            }

            // Prepare and execute
            $stmt = $conn->prepare($query);

            if ($params) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new Exception("Query execution failed: " . $stmt->error);
            }

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
    error_log("Browse process error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>