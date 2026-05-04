<?php
require('../../db-connect.php');

header('Content-Type: application/json'); 

if (isset($_GET['id'])) {
    $user_id = base64_decode(urldecode($_GET['id']));
    $new_password = password_hash("Default123!", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE accounts SET USER_PASSWORD = ? WHERE USER_ID = ?");
    $stmt->bind_param("si", $new_password, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Password reset to Default123!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
    }
    $stmt->close();
}
exit;
?>
