<?php
    session_start();
    require('../../db-connect.php');

    if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'Super Admin'){
        die('Access denied.');
    }

    $errors = [];

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        goto render_form;
    }

    $email = trim($_POST['email'] ?? '');
    $fName = trim($_POST['lName'] ?? '');
    $lName = trim($_POST['fName'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if(empty($fName)) $errors['fName'] = "Firstname is required.";
    if(empty($lName)) $errors['lName'] = "Lastname is required.";
    if(empty($email)) $errors['email'] = "Email is required.";
    if(strlen($password) < 8) $errors['password'] = "Password must be at least 8 character long.";

    if(!empty($errors)){
    goto render_form;
    }


    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    $role = 'Admin';
        
    $sql = "INSERT INTO accounts (USER_FIRST_NAME, USER_LAST_NAME, USER_EMAIL, USER_PASSWORD, USER_ROLE) VALUES (?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
        
    if (!$stmt) die('Prepare Error: '.$conn->error);

    $stmt->bind_param("sssss",$fName,$lName,$email,$hashed_pass,$role);

    if(!$stmt->execute()) die('Execution Error: '.$stmt->error);

    echo "<script>alert('New Admin Successfuly Added'); window.location = 'super-admin-dashboard.php';</script>";
    exit;

    render_form:
?>