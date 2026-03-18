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
    <link rel="stylesheet" href="style.css?v=1.1">
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1>Admin Control Panel</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_role']);?></p>
            </div>
            <nav class="admin-nav">
                <button onclick="showSection('pendingSection')" class="nav-btn active">Pending Items</button>
                <button onclick="showSection('archiveSection')" class="nav-btn">Archive Item</button>
                <button onclick="showSection('userSection')" class="nav-btn">Manage Users</button>
                <hr>
                <a href="../../index.php" class="nav-link">Back to Store</a>
                <a href="../../index.php?action=logout" class="nav-link logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">

            <!--PENDING ITEM SECTION-->
            <div id="pendingSection" class="tab-content">
                <div class="content-header">
                    <h2>Pending Approval</h2>
                </div>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Description</th>
                                <th class="action-cells">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result->num_rows > 0):?>
                                <?php
                                    while($item = $result->fetch_assoc()):
                                    $hashed_id =urlencode(base64_encode($item['ITEM_ID']));
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['ITEM_NAME']);?></td>
                                    <td><?php echo htmlspecialchars($item['ITEM_DESCRIPTION']);?></td>
                                    <td>
                                        <a href="approve-item.php?id=<?php echo $hashed_id; ?>" class="btn btn-approve">Appprove</a>
                                        <a href="archive-item.php?id=<?php echo $hashed_id; ?>" class="btn btn-archive">Archive</a>
                                    </td>
                                </tr>
                                <?php endwhile;?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="empty-state">
                                        <div class="empty-box">
                                            <p>No Item waitng for approval.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ARCHIVE SECTION -->
            <div id="archiveSection" class="tab-content hidden">
                <div class="content-header">
                    <h2>Archived Items</h2>
                </div>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Status</th>
                                <th class="action-cells">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $sql_archive = "SELECT ITEM_ID, ITEM_NAME FROM items WHERE ITEM_STATUS = 'Archived'";
                                $res_archive = $conn->query($sql_archive);
                                
                                if($res_archive->num_rows > 0):
                                    while($archive = $res_archive->fetch_assoc()):
                                        $hashed_id = urlencode(base64_encode($archive['ITEM_ID']));
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($archive['ITEM_NAME']);?></td>
                                <td><span class="badge">Archived</span></td>
                                <td>
                                    <a href="restore-item.php?id=<?php echo $hashed_id;?>" class="btn btn-approve">Restore</a>
                                </td>
                            </tr>
                            <?php endwhile;?>
                            <?php else: ?>
                            <tr>
                                <td colspan="3" class="empty-state">
                                    <div class="empty-box">
                                        <p>No archived items found.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- USER SECTION -->
            <div id="userSection" class="tab-content hidden">
                <div class="content-header">
                    <h2>Manage Users</h2>
                </div>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="action-cells">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sql_users = "SELECT USER_ID, USER_EMAIL, USER_ROLE FROM users";
                                $res_users = $conn->query($sql_users);

                                if($res_users && $res_users->num_rows > 0):
                                    while($user = $res_users->fetch_assoc()):
                                        $hashed_uid = urlencode(base64_encode($user['USER_ID'])); 
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['USER_EMAIL']); ?></td>
                                <td><?php echo htmlspecialchars($user['USER_ROLE']); ?></td>
                                <td>
                                    <a href="reset-password.php?id=<?php echo $hashed_uid; ?>" class="btn btn-approve">Reset</a>
                                    <a href="archive-user.php?id=<?php echo $hashed_uid; ?>" class="btn btn-archive" onclick="return confirm('Archive this User?')">Archive</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="3" class="empty-state">
                                    <div class="empty-box">
                                        <p>No users found in database.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
<script src="script.js"></script>
</html>