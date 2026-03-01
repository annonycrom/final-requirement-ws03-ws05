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

    echo "<script>alert('Item Successfuly Archive'); window.location = 'admin-dashboard.php';</script>";
    exit;
?>