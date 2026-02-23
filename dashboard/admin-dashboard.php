<?php
    session_start();
    require('../db-connect.php');

    if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'Admin'){
        header('Location: ../index.php?error=unauthorized');
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
    <a href="../index.php">Back to Store</a> | <a href="../index.php?mode=login">Logout</a>
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
                    <a href="archive-item.php?id=<?php echo $hashed_id; ?>"></a>
                </td>
            </tr>
            <?php endwhile;?>
        </tbody>
    </table>
</body>
</html>