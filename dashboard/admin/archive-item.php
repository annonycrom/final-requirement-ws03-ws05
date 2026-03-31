<?php
    session_start();
    require('../../db-connect.php');

    if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] == 'Regular'){
        die('Access denied.');
    }

    $hahed_id = $_GET['id'];
    $decode_id = base64_decode(urldecode($hahed_id));

    if(!is_numeric(trim($decode_id))){
        die('Invalid Item.');
    }

    $sql = "UPDATE items SET ITEM_STATUS = 'Archived' WHERE ITEM_ID = ?";
    $stmt = $conn->prepare($sql);
    
    if(!$stmt){
        die('Prepare Error: '.$conn->error);
    }
    
    $stmt->bind_param('i',$decode_id);

    if(!$stmt->execute()){
        die('Execution Error: '.$stmt->error);
    }

    if($stmt->affected_rows > 0){
       $response = [
           'status' => 'success',
           'message' => 'Item archived successfully.'
       ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'No item found with the provided ID.'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
?>