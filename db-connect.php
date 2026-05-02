<?php
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'app_accounts';

    $conn = new mysqli($db_host,$db_user,$db_pass,$db_name);

    if($conn->connect_error){
        die("Connection Failed ".$conn->connection_error);
    }

    $conn->set_charset("utf8mb4");

    if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_user'])) {
    $cookie_token = $_COOKIE['remember_user'];

    $sql = "SELECT USER_ID, USER_ROLE FROM accounts WHERE REMEMBER_TOKEN = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cookie_token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $user['USER_ID'];
        $_SESSION['user_role'] = $user['USER_ROLE'];
        $_SESSION['logged_in'] = true;
    }
    }
?>