<?php
    session_start();
    require('../../db-connect.php');
    require('../../logs.php');
    header('Content-Type: application/json');
    
    if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] === 'Regular'){
        die('Access denied.');
    }

    $errors = [];

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
          echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
        exit;
    }

    $email = trim($_POST['email'] ?? '');
    $fName = trim($_POST['fName'] ?? '');
    $lName = trim($_POST['lName'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if(empty($fName)) $errors['fName'] = "Firstname is required.";
    if(empty($lName)) $errors['lName'] = "Lastname is required.";
    if(empty($email)) $errors['email'] = "Email is required.";
    if(strlen($password) < 8) $errors['password'] = "Password must be at least 8 character long.";

     if(!empty($errors)){

        $response  = [
        'status' => 'error',
        'message' => implode(" ", $errors),
        'errors' => $errors
        ];
    echo json_encode($response);
    exit;
    }

    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    $role = 'Regular';
        
    $sql = "INSERT INTO accounts (USER_FIRST_NAME, USER_LAST_NAME, USER_EMAIL, USER_PASSWORD, USER_ROLE) VALUES (?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
        
    if (!$stmt) die('Prepare Error: '.$conn->error);

    $stmt->bind_param("sssss",$fName,$lName,$email,$hashed_pass,$role);

    if($stmt->execute()){
        // echo 'added successful';
        $response =[
            'status' => 'success',
            'message' => 'New user added'
        ];

        $performer_id = $_SESSION['user_id'];
        $action = "Add User";
        $details = "Addedd regular user";
        record_activity($conn, $performer_id, $action, $details);
    
    }else{
     
        // die('Execution Error: '.$stmt->error);
        $response = [
            'status' => 'error',
            'message' => 'Execution Error: '.$stmt->error
        ];
    }
    


    echo json_encode($response);
    exit;

?>