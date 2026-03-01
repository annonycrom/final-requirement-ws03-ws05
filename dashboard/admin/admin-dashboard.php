<?php
    session_start();
    require('../../db-connect.php');

    if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'Admin'){
        header('Location: ../../index.php?error=unauthorized');
        exit;
    }

    $sql = "SELECT ITEM_ID, ITEM_NAME, ITEM_DESCRIPTION FROM items WHERE ITEM_STATUS = 'Pending'";
    $result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Control Panel</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_role']);?></p>
    <a href="../../index.php">Back to Store</a> | <a href="../../index.php?action=logout">Logout</a>
    <h2>Pending Approval</h2>
    <table border = "1">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                    while($item = $result->fetch_assoc()):
                        $hashed_id =urlencode(base64_encode($item['ITEM_ID']));
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['ITEM_NAME']);?></td>
                <td><?php echo htmlspecialchars($item['ITEM_DESCRIPTION']);?></td>
                <td>
                    <a href="approve-item.php?id=<?php echo $hashed_id; ?>">Appprove</a>
                    <a href="archive-item.php?id=<?php echo $hashed_id; ?>">Archive</a>
                </td>
            </tr>
            <?php endwhile;?>
        </tbody>
    </table>
    <hr>
    <h2>Restore Item</h2>
    <table border = "1">
        <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
        </thead>
        <tbody>
            <?php 
                $sql_archive = "SELECT ITEM_ID, ITEM_NAME FROM items WHERE ITEM_STATUS = 'Archived'";
                $res_archive = $conn->query($sql_archive);
                
                while($archive = $res_archive->fetch_assoc()):
                    $hashed_id = urlencode(base64_encode($archive['ITEM_ID']));
            ?>
            <tr>
                <td><?php echo htmlspecialchars($archive['ITEM_NAME']);?></td>
                <td><span>Archive</span></td>
                <td>
                    <a href="restore-item.php?id=<?php echo $hashed_id;?> ">Restore Item</a>
                </td>
            </tr>
            <?php endwhile;?>
        </tbody>
    </table>
    <hr>
    <h2>Manage User</h2>
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
                    <a href="archive-user.php?id=<?php echo $hashed_uid; ?>" onclick = "return confirm('Archive this User?')">Remove (Archive)</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>