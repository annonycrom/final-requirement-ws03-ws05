<?php
    session_start();
    require('../../db-connect.php');

    if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] === 'Regular'){
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
    $role = 'Regular';
        
    $sql = "INSERT INTO accounts (USER_FIRST_NAME, USER_LAST_NAME, USER_EMAIL, USER_PASSWORD, USER_ROLE) VALUES (?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
        
    if (!$stmt) die('Prepare Error: '.$conn->error);

    $stmt->bind_param("sssss",$fName,$lName,$email,$hashed_pass,$role);

    if(!$stmt->execute()) die('Execution Error: '.$stmt->error);

    echo "<script>alert('New User Successfuly Added'); window.location = 'admin-dashboard.php';</script>";
    exit;

    render_form:
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
</head>
<body>
    <h2>Add New User</h2>
    <form action="add-admin.php" method="post">
        <input type="text" name="fName" id="fName" placeholder = "Add Admin Fistname" value = "<?php echo htmlspecialchars($fName ?? ''); ?>" required>
        <?php if(isset($errors['fName'])) :?>
            <p><?php echo $errors['fName'];?></p>
        <?php endif;?>
        

        <input type="text" name="lName" id="lName" placeholder = "Add Admin Lastname" value = "<?php echo htmlspecialchars($lName ?? ''); ?>" required>
        <?php if(isset($errors['lName'])) :?>
            <p><?php echo $errors['lName'];?></p>
        <?php endif;?>


        <input type="email" name="email" id="email" placeholder = "Add Admin Email" value = "<?php echo htmlspecialchars($email ?? ''); ?>" required>
        <?php if(isset($errors['email'])) :?>
            <p><?php echo $errors['email'];?></p>
        <?php endif;?>

        
        <input type="password" name="password" id="password" placeholder = "Temporary Password" required>
        <?php if(isset($errors['password'])) :?>
            <p><?php echo $errors['password'];?></p>
        <?php endif;?>
        <input type="submit" value="Create Admin">
        <a href="admin-dashboard.php">Back</a>
    </form>
</body>
</html>