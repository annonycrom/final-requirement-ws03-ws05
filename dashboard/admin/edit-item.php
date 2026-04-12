<?php
    session_start();
    require('../../logs.php');
    header('Content-Type: application/json');
    require('../../db-connect.php');

    if(!empty($_POST)){
        $id = base64_decode(urldecode($_POST['id']));
        $name = $_POST['name'];
        $desc = $_POST['desc'];
        $price = $_POST['price'];

        $image_name = NULL;
        if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK){
            $target_dir = "../../uploads/";
            $file_etension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $image_name = time() . '_' . uniqid() . '.' . $file_etension;
            $target_file = $target_dir . $image_name;

            if(!move_uploaded_file($_FILES['file']['tmp_name'],$target_file)){
                echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);  
            }
        }
            
        $get_old = $conn->prepare("SELECT ITEM_NAME, ITEM_DESCRIPTION, ITEM_PRICE, ITEM_IMAGE FROM items WHERE ITEM_ID = ?");
        $get_old->bind_param('i', $id);
        $get_old->execute();
        $old_item = $get_old->get_result()->fetch_assoc();


        if($image_name){
        $sql = "UPDATE items SET ITEM_NAME = ?, ITEM_DESCRIPTION = ?, ITEM_PRICE = ?, ITEM_IMAGE = ? WHERE ITEM_ID =?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsi", $name, $desc, $price, $image_name, $id);
        
        }else{
            $sql = "UPDATE items SET ITEM_NAME = ?, ITEM_DESCRIPTION = ?, ITEM_PRICE = ? WHERE ITEM_ID =?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdi", $name, $desc, $price, $id);
        }

        if( $stmt->execute()){

            $changes = [];

            if($old_item['ITEM_NAME'] != $name){
                $changes[] = "Name: '{$old_item['ITEM_NAME']}' -> {$name}";
            }
            if($old_item['ITEM_DESCRIPTION'] != $desc){
                $changes[] = "Description: {$desc}";
            }
            if($old_item['ITEM_PRICE'] != $price){
                $changes[] = "Price: '{$old_item['ITEM_PRICE']}' -> {$price}";
            }

            $details_change = !empty($changes) ? implode(", ",$changes) : "No fields changes detected";
            $final_details = "Update Item #$id. Changes: ". $details_change;

            $performer_id = $_SESSION['user_id'];
            $action = "Item Update";

            record_activity($conn, $performer_id, $action, $final_details);

            $response = [
                'status' => 'success',
                'message' => 'Item updated successfully.'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Database error: ' . $stmt->error
            ];
        }  
    }else{
        $response = ['status' => 'error', 'message' => 'No data received.'];
    }
        echo json_encode($response);

    exit;
?>