<?php
    session_start();
    require('../db-connect.php');

    if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] === 'Regular'){
        die("Access Denied.");
    }

    $hashed_id = $_GET['id'] ?? '';

    $decoded_id = base64_decode(urldecode($hashed_id));

    if(is_numeric(trim($decoded_id))){
        $sql = "UPDATE items SET ITEM_STATUS = 'Approved' WHERE ITEM_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i",$decoded_id);

        if($stmt->execute()){

             if ($stmt->affected_rows > 0) {
                echo "<script>alert('Item Approved Successfully!'); window.location = 'admin-dashboard.php';</script>";
            } else {
                echo "<script>alert('No changes made. Item might already be approved.'); window.location = 'admin-dashboard.php';</script>";
            }
            exit;
        }
    } else {
        die("Invalid Item ID. Decoded value: " . htmlspecialchars($decoded_id));
    }
?>
