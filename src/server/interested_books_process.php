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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $user_id = $_SESSION['user_id'];

        if ($_POST['action'] === 'get_incoming_interests') {
            // Get interests where user is the seller (people interested in user's books)
            $query = "SELECT 
                        bi.interest_id,
                        bi.status,
                        bi.interest_date,
                        b.book_id,
                        b.book_name,
                        b.current_selling_price as book_price,
                        u_buyer.user_id as buyer_id,
                        CONCAT(u_buyer.first_name, ' ', u_buyer.last_name) as buyer_name,
                        u_buyer.college_name as buyer_college,
                        u_buyer.email as buyer_email,
                        u_buyer.phone_primary as buyer_phone
                      FROM book_interests bi
                      JOIN books b ON bi.book_id = b.book_id
                      JOIN users u_buyer ON bi.buyer_id = u_buyer.user_id
                      WHERE bi.seller_id = ?
                      ORDER BY bi.interest_date DESC";

            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $interests = [];
            while ($row = $result->fetch_assoc()) {
                $interests[] = $row;
            }

            $stmt->close();

            echo json_encode(['success' => true, 'interests' => $interests]);

        } elseif ($_POST['action'] === 'get_outgoing_interests') {
            // Get interests where user is the buyer (books user is interested in)
            $query = "SELECT 
                        bi.interest_id,
                        bi.status,
                        bi.interest_date,
                        b.book_id,
                        b.book_name,
                        b.current_selling_price as book_price,
                        u_seller.user_id as seller_id,
                        CONCAT(u_seller.first_name, ' ', u_seller.last_name) as seller_name,
                        u_seller.college_name as seller_college
                      FROM book_interests bi
                      JOIN books b ON bi.book_id = b.book_id
                      JOIN users u_seller ON bi.seller_id = u_seller.user_id
                      WHERE bi.buyer_id = ?
                      ORDER BY bi.interest_date DESC";

            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $interests = [];
            while ($row = $result->fetch_assoc()) {
                $interests[] = $row;
            }

            $stmt->close();

            echo json_encode(['success' => true, 'interests' => $interests]);

        } elseif ($_POST['action'] === 'update_interest_status') {
            $interest_id = intval($_POST['interest_id']);
            $status = $_POST['status'];
            $book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : null;

            // Verify user owns this interest (is the seller)
            $verify_query = "SELECT seller_id FROM book_interests WHERE interest_id = ?";
            $verify_stmt = $conn->prepare($verify_query);
            $verify_stmt->bind_param('i', $interest_id);
            $verify_stmt->execute();
            $verify_result = $verify_stmt->get_result();

            if ($verify_result->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Interest not found']);
                exit();
            }

            $interest_data = $verify_result->fetch_assoc();
            if ($interest_data['seller_id'] != $user_id) {
                echo json_encode(['success' => false, 'message' => 'Not authorized to update this interest']);
                exit();
            }
            $verify_stmt->close();

            // Update interest status
            $update_query = "UPDATE book_interests SET status = ?, approved_at = NOW() WHERE interest_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param('si', $status, $interest_id);

            if ($update_stmt->execute()) {
                // If marking as sold, update the book status
                if ($status === 'sold' && $book_id) {
                    $book_query = "UPDATE books SET status = 'sold' WHERE book_id = ?";
                    $book_stmt = $conn->prepare($book_query);
                    $book_stmt->bind_param('i', $book_id);
                    $book_stmt->execute();
                    $book_stmt->close();

                    // Mark all other interests for this book as sold
                    $other_interests_query = "UPDATE book_interests SET status = 'sold' WHERE book_id = ? AND interest_id != ?";
                    $other_stmt = $conn->prepare($other_interests_query);
                    $other_stmt->bind_param('ii', $book_id, $interest_id);
                    $other_stmt->execute();
                    $other_stmt->close();
                }

                $update_stmt->close();
                echo json_encode(['success' => true, 'message' => 'Interest status updated successfully']);
            } else {
                $update_stmt->close();
                throw new Exception("Failed to update interest status: " . $conn->error);
            }

        } elseif ($_POST['action'] === 'get_seller_contact') {
            $interest_id = intval($_POST['interest_id']);

            // Verify user is the buyer for this interest
            $verify_query = "SELECT buyer_id, seller_id FROM book_interests WHERE interest_id = ? AND status = 'approved'";
            $verify_stmt = $conn->prepare($verify_query);
            $verify_stmt->bind_param('i', $interest_id);
            $verify_stmt->execute();
            $verify_result = $verify_stmt->get_result();

            if ($verify_result->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Interest not found or not approved']);
                exit();
            }

            $interest_data = $verify_result->fetch_assoc();
            if ($interest_data['buyer_id'] != $user_id) {
                echo json_encode(['success' => false, 'message' => 'Not authorized to view this contact']);
                exit();
            }
            $verify_stmt->close();

            // Get seller contact info
            $contact_query = "SELECT first_name, last_name, email, phone_primary, college_name 
                            FROM users WHERE user_id = ?";
            $contact_stmt = $conn->prepare($contact_query);
            $contact_stmt->bind_param('i', $interest_data['seller_id']);
            $contact_stmt->execute();
            $contact_result = $contact_stmt->get_result();

            if ($contact_result->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Seller not found']);
                exit();
            }

            $seller = $contact_result->fetch_assoc();
            $contact_stmt->close();

            echo json_encode([
                'success' => true,
                'seller' => [
                    'name' => $seller['first_name'] . ' ' . $seller['last_name'],
                    'email' => $seller['email'],
                    'phone' => $seller['phone_primary'],
                    'college' => $seller['college_name']
                ]
            ]);

        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }

} catch (Exception $e) {
    error_log("Interested books process error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>