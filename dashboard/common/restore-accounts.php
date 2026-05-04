<?php
    session_start();
    require('../../db-connect.php');
    require('../../logs.php');
    header('Content-Type: application/json');
    
    // Security: Only allow Super Admin to restore accounts
    if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'Super Admin'){
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    }

    // Get user ID from the URL
    $userId = $_GET['id'] ?? null;

    if(!$userId){
        echo json_encode(['status' => 'error', 'message' => 'Invalid User ID.']);
        exit;
    }

    // Restore logic: Update status back to Active
    $sql = "UPDATE accounts SET USER_STATUS = 'Active' WHERE USER_ID = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $userId);

    if($stmt->execute()){
        // Log the restoration activity
        $performer_id = $_SESSION['user_id'];
        record_activity($conn, $performer_id, "Restore Account", "Restored User ID: $userId");

        echo json_encode([
            'status' => 'success',
            'message' => 'Account restored successfully!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to restore account: ' . $stmt->error
        ]);
    }
    exit;
?>
