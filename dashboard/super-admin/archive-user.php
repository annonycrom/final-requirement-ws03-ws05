<?php
     session_start();

     require('../../db-connect.php');


     if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] === 'Regular'){
        die('Access denied.');
     }

     $hashed_id = $_GET['id'] ?? '';
     $decode_id = base64_decode(urldecode($hashed_id));

     if(!is_numeric($decode_id)){
        die('Invalid ID.');
     }

    $sql = "UPDATE accounts SET USER_STATUS = 'Archived' WHERE USER_ID = ?";
    $stmt = $conn->prepare($sql);

    if(!$stmt) die('Prepare Error: ' .$conn->error);

    $stmt->bind_param('i',$decode_id);

    if(!$stmt->execute()) die('Execution Error: '. $stmt->error);

    echo "<script>alert('Account Successfuly Archive/Remove.'); window.location = 'super-admin-dashboard.php';</script>";
 ?>