<?php
    require('../db-connect.php');
    function sanitize($input){
        if(empty($input)){
            return null;
        }

        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        return $input;
    }

    $first_name = sanitize($_POST['first_name']?? '');
    $last_name = sanitize($_POST['last_name']?? '');
    $email = sanitize($_POST['email']?? '');
    $password = $_POST['password']?? '';

    if (!$first_name || !$last_name || !$email || !$password){
        echo"Please fill all required fields.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        exit;
    }
    
    $password_hashed = password_hash($password,PASSWORD_DEFAULT);

    echo "<p>$first_name</p>";
    echo "<p>$last_name</p>";
    echo "<p>$email</p>";
    echo "<p>$password_hashed</p>";

    $sql = "INSERT INTO accounts (USER_FIRST_NAME,USER_LAST_NAME,USER_EMAIL,USER_PASSWORD) VALUES (?,?,?,?)";
    $stmt = $conn->prepare($sql);

    if($stmt){
        $stmt->bind_param("ssss",$first_name,$last_name,$email,$password_hashed);

        if($stmt->execute()){
            echo "Registration Success.";
        }else{
            echo "Error ". $conn->error;
        }
        $stmt->close();
    }else{
        echo"Database Error.". $conn->error;
    }
?>