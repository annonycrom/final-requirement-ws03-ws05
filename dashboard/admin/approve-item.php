<?php
    session_start();
    require('../../db-connect.php');

    if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] === 'Regular'){
        die("Access Denied.");
    }

    $hashed_id = $_GET['id'] ?? '';

    $decoded_id = base64_decode(urldecode($hashed_id));

    if(is_numeric(trim($decoded_id))){
        $sql = "UPDATE items SET ITEM_STATUS = 'Approved' WHERE ITEM_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i",$decoded_id);

        header('Content-Type: application/json');
        if($stmt->execute()){

             if ($stmt->affected_rows > 0) {
                $response = [
                    'status' => 'success',
                    'message' => 'Item approved successfully.'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'No item found with the provided ID.'
                ];
            }
            echo json_encode($response);
            exit;
        }
    }
?>
