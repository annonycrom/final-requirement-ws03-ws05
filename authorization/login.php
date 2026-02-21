<?php
    session_start();
    require("../db-connect.php");

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)){
        die("Please fill both fields.");
    }

    $sql = "SELECT USER_ID, USER_PASSWORD , USER_ROLE FROM accounts WHERE USER_EMAIL = ? LIMIT 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt){
        die("Database Error: " . $conn->error);
        }

        $stmt -> bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();

    if (!$user = $result->fetch_assoc()){
        die("No account found.") ;   
    }
    if(!password_verify($password,$user['USER_PASSWORD'])){
        die ("Invalid password.");    
    }
    $_SESSION['user_role'] = $user['USER_ROLE'];
    $_SESSION['user_id'] = $user['USER_ID'];
    $_SESSION['logged_in'] = true;

    header('Location: ../dashboard/user-dashboard.php');
    exit;
?>