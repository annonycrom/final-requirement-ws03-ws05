<?php
    session_start();
    require('../db-connect.php');

    if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'Super Admin'){
        header('Location: ../index.php?error=unauthorized');
    }
    $sql = "SELECT USER_ID, USER_EMAIL, USER_ROLE FROM accounts WHERE USER_ROLE =  'Admin'";
    $result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Welcome, Super Admin</h1>
    <nav>
        <a href="add-admin.php">Add New Admin</a>
        <a href="admin-dashboard.php">Item Management</a>
        <a href="../index.php?action=logout">Logout</a>
    </nav>
    <h2>Manage Admins</h2>
    <table border = "1">
        <thead>
            <tr>
                <th>Email</th>
                <th>Role</th>
                <th>Ations</th>
            </tr>
        </thead>
        <tbody>
            <?php
                while($user = $result->fetch_assoc()):
                    $hashed_uid = urlencode(base64_encode($user['USER_ID'])); 
            ?>
            <tr>
                <td><?php echo htmlspecialchars($user['USER_EMAIL']); ?></td>
                <td><?php echo htmlspecialchars($user['USER_ROLE']); ?></td>
                <td>
                    <a href="reset-password.php?id=<?php echo $hashed_uid; ?>">Reset Password</a>
                    <a href="archive-user.php?id=<?php echo $hashed_uid; ?>" onclick = "return confirm('Archive this Admin?')">Remove (Archive)</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>