<?php
    session_start();
    require("../db-connect.php");

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)){
        die("Please fill both fields.");
    }

    $sql = "SELECT USER_ID, USER_PASSWORD , USER_ROLE, USER_STATUS FROM accounts WHERE USER_EMAIL = ? LIMIT 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt){
        die("Database Error: " . $conn->error);
        }

        $stmt -> bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();

    if (!$user = $result->fetch_assoc()){
        die("Invalid Credentials") ;   
    }
    if(!password_verify($password,$user['USER_PASSWORD'])){
        die ("Invalid Credentials");    
    }

    if($user['USER_STATUS'] !== 'Active'){
        die('Access denied: This Account has been removed. Please contact the management for more info.');
    }

    $_SESSION['user_role'] = $user['USER_ROLE'];
    $_SESSION['user_id'] = $user['USER_ID'];
    $_SESSION['logged_in'] = true;

    if ($_SESSION['user_role'] === 'Super Admin'){
        header('Location: ../dashboard/super-admin/super-admin-dashboard.php');
    }elseif($_SESSION['user_role'] === 'Admin'){
        header('Location: ../dashboard/admin/admin-dashboard.php');
    }else{
        header('Location: ../index.php');
    }
    exit;
?>