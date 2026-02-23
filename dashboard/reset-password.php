<?php
    session_start();
    require('../db-connect.php');

    if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] === 'Regular'){
        die('Access Denied.');
    }

    $hashed_id = $_GET['id'] ?? '';
    $decode_id = base64_decode(urldecode($hashed_id));

    if (!is_numeric($decode_id)){
        die('Invalid ID.');
    }

    $temp_pass = password_hash('123456', PASSWORD_DEFAULT);
        
    $sql = "UPDATE accounts SET USER_PASSWORD = ? WHERE USER_ID = ? ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param('si', $temp_pass, $decode_id);

    if(!$stmt->execute()){
        die('Execution Error: '.$stmt->error);
    }
    echo "<script>alert('Password reset to 123456'); window.location='super-admin-dashboard.php';</script>";
?>