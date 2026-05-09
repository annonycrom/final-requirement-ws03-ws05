<?php
    session_start();
    require('../db-connect.php');

    if (!isset($_SESSION['logged_in'])){
        header("Location: ../authorization/auth.php?mode=login");
        exit;
    }
    function validProd($name){
            return !empty(trim($name)) ? trim($name) : null;
        }

        $errors = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $name = validProd($_POST['item_name']) ?? '';
        $desc = validProd($_POST['item_description']) ?? '';
        $image = 'default.png';
        if (!$name) $errors['name'] = "Product Name cannot be empty.";
        if (!$desc) $errors['desc'] = "Product Description cannot be empty.";
        if (!$image) $errors['image'] = "Invalid Image format.";

        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === 0) {
            $upload_dir = "../uploads/"; 
            $file_ext = pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION);
            
            // This creates a unique name (e.g., 1672531200_63b1.png)
            $new_name = time() . "_" . uniqid() . "." . $file_ext;
            $target_path = $upload_dir . $new_name;

            if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_path)) {
                $image = $new_name; // Update $image from 'default.png' to the real filename
            } else {
                $errors['image'] = "Failed to upload image.";
            }
        }


        if(empty($errors)){
            
            $user_id = $_SESSION['user_id'];
            $status = 'Pending';
    
            $sql = "INSERT INTO items (ITEM_NAME, ITEM_DESCRIPTION, ITEM_IMAGE, ITEM_STATUS, ADDED_BY) VALUES (?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            
            if(!$stmt){
                die("Database Prepare Error: ". $conn->error);
            }
    
            $stmt->bind_param('ssssi',$name,$desc,$image,$status,$user_id);
            
            if(!$stmt->execute()){
                die("Execution Error: ".$stmt->error);
            }
                
            echo "<script>alert('Item submmited! Waiting for Admin approval.'); window.location = '../index.php'; </script>";
        }


        
    }
?>