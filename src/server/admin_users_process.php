<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

include('connection.php');

header('Content-Type: application/json');

try {
    if (isset($_GET['action'])) {

        if ($_GET['action'] === 'get_users') {
            $search = $_GET['search'] ?? '';

            $query = "SELECT 
                        user_id, email, first_name, last_name, college_name, 
                        phone_primary, trust_score, created_at
                    FROM users 
                    WHERE 1=1";

            $params = [];
            $types = '';

            if (!empty($search)) {
                $query .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR college_name LIKE ?)";
                $searchTerm = '%' . $search . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= 'ssss';
            }

            $query .= " ORDER BY user_id DESC";

            if ($params) {
                $stmt = $conn->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $conn->query($query);
            }

            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }

            if (isset($stmt))
                $stmt->close();

            echo json_encode([
                'success' => true,
                'users' => $users
            ]);

        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'No action specified']);
    }

} catch (Exception $e) {
    error_log("Admin users process error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

$conn->close();
?>