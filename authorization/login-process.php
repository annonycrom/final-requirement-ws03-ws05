<?php
    session_start();

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
            die('INVALID ACCESS.');
        }
    }

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
        header('Location: auth.php?error=invalid&mode=login');
        exit;   
    }
    if(!password_verify($password,$user['USER_PASSWORD'])){
        header('Location: auth.php?error=invalid&mode=login');
        exit;    
    }

    if(trim($user['USER_STATUS']) !== 'Active'){
        header('Location: auth.php?error=invalid&mode=login');
        exit;
    }


    // uncomment this for debugging
    // if (!$user = $result->fetch_assoc()){
    //     die("Debug: Email not found in database.");
    // }
    // if(!password_verify($password, $user['USER_PASSWORD'])){
    //     die("Debug: Password verify failed. DB Hash: " . $user['USER_PASSWORD']);
    // }
    // if($user['USER_STATUS'] !== 'Active'){
    //     die("Debug: Status is " . $user['USER_STATUS'] . " instead of Active.");
    // }

    $_SESSION['user_role'] = $user['USER_ROLE'];
    $_SESSION['user_id'] = $user['USER_ID'];
    $_SESSION['logged_in'] = true;

    if(isset($_POST['remember-me'])){
        $token = bin2hex(random_bytes(16));
        setcookie("remember_user", $token, time()+(30*24*60*60),"/","", false, true);
        $update_sql = "UPDATE accounts SET REMEMBER_TOKEN = ? WHERE USER_ID = ? ";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('si', $token, $user['USER_ID']);
        $update_stmt->execute();
    }

    if ($_SESSION['user_role'] === 'Super Admin'){
        header('Location: ../dashboard/super-admin/super-admin-dashboard.php');
    }elseif($_SESSION['user_role'] === 'Admin'){
        header('Location: ../dashboard/admin/admin-dashboard.php');
    }else{
        header('Location: ../index.php');
    }
    exit;
?>