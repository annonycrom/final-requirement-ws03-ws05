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
    if($_SERVER['REQUEST_METHOD'] ===  'POST'){
        $name = validProd($_POST['item_name']) ?? '';
        $desc = validProd($_POST['item_description']) ?? '';
        $image = 'default.jpg';
        if (!$name) $errors['name'] = "Product Name cannot be empty.";
        if (!$desc) $errors['desc'] = "Product Description cannot be empty.";
        if (!$image) $errors['image'] = "Invalid Image format.";

        if(empty($errors)){
            
            $user_id = $_SESSION['user_id'];
            $status = 'Pending';
    
            $sql = "INSERT INTO items (ITEM_NAME, ITEM_DESCRIPTION, ITEM_STATUS, ADDED_BY) VALUES (?,?,?,?)";
            $stmt = $conn->prepare($sql);
            
            if(!$stmt){
                die("Database Prepare Error: ". $conn->error);
            }
    
            $stmt->bind_param('sssi',$name,$desc,$status,$user_id);
            
            if(!$stmt->execute()){
                die("Execution Error: ".$stmt->error);
            }
                
            echo "<script>alert('Item submmited! Waiting for Admin approval.'); window.location = '../index.php'; </script>";
        }


        
    }
?>