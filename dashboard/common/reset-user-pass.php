<?php
require('../../db-connect.php');
require('../../logs.php');


header('Content-Type: application/json'); 

if (isset($_GET['id'])) {
    $raw_id = $_GET['id'];
    $clean_id = str_replace(' ', '+', $raw_id);
    $user_id = base64_decode($clean_id);
    $new_password = password_hash("Default123", PASSWORD_DEFAULT);

    // // --- DIAGNOSTIC: Check if ID exists first ---
    // $check = $conn->prepare("SELECT USER_ID FROM accounts WHERE USER_ID = ?");
    // // Use "s" for string to be safe against all ID types
    // $check->bind_param("s", $user_id); 
    // $check->execute();
    // $check->store_result();
    
    // if ($check->num_rows === 0) {
    //     echo json_encode([
    //         'status' => 'error',
    //         'message' => 'The User ID does not exist in the database.',
    //         'debug_info' => [
    //             'raw_get_id' => $raw_id,
    //             'decoded_id_result' => $user_id,
    //             'decoded_id_length' => strlen($user_id)
    //         ]
    //     ]);
    //     exit;
    // }
    // $check->close();
    // // --------------------------------------------


    $stmt = $conn->prepare("UPDATE accounts SET USER_PASSWORD = ? WHERE USER_ID = ?");
    $stmt->bind_param("si", $new_password, $user_id);

    if ($stmt->execute()) {
        if($stmt->affected_rows > 0){

            $performer_id = $_SESSION['user_id'];
            record_activity($conn, $performer_id, "Restore Account", "Reset User password: $user_id");

            echo json_encode(['status' => 'success', 'message' => 'Password reset to Default123!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
        }
    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'No ID provided.'
    ]);
}
exit;
}
?>
