<?php
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    require('../../db-connect.php');

    function record_activity($conn, $performer_id, $action, $details){
        $stmt = $conn->prepare("INSERT INTO activity_logs (performer_id, action_type, details) VALUES (?,?,?)");
        $stmt->bind_param('iss', $performer_id, $action, $details);
        return $stmt->execute();
    }

    function get_all_logs($conn){
        $sql = "SELECT l.*, a.USER_EMAIL FROM activity_logs l LEFT JOIN accounts a ON l.performer_id = a.USER_ID ORDER BY l.created_at DESC LIMIT 50";
        $result = $conn->query($sql);

        if(!$result){
            error_log('SQL Error: '.$conn->error);
        }
        return $result;
    }
?>