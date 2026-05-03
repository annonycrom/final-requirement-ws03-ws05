<?php
     session_start();

     require('../../db-connect.php');

     header('Content-Type: application/json');
     
     if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] === 'Regular'){
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
        exit;
     }

     $hashed_id = $_GET['id'] ?? '';
     $decode_id = base64_decode(urldecode($hashed_id));

     if(!is_numeric($decode_id)){
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
        exit;
     }

    $sql = "UPDATE accounts SET USER_STATUS = 'Archived' WHERE USER_ID = ?";
    $stmt = $conn->prepare($sql);

    if(!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare Error.']);
        exit;
    }

    $stmt->bind_param('i',$decode_id);

    if($stmt->execute()){
        // Return success JSON
        echo json_encode(['status' => 'success', 'message' => 'Account Successfully Archived.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Execution Error.']);
    }
    
    $stmt->close();
 ?>