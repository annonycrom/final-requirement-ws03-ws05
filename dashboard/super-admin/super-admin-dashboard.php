<?php
    session_start();
    require('../../db-connect.php');
    require('../../logs.php');

    if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'Super Admin'){
        header('Location: ../../index.php?error=unauthorized');
    }
    $sql = "SELECT USER_ID, USER_EMAIL, USER_ROLE FROM accounts WHERE USER_ROLE =  'Admin' AND USER_STATUS = 'Active'";
    $result = $conn->query($sql);

    $log_result = get_all_logs($conn);
    if (!$log_result) { echo "Query Error: " . $conn->error; } 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=1.1">
    <title>Document</title>
</head>
<body>
    <div class="admin-wrapper">
        <aside>
            <div class="sidebar-header">
                <h2>Super Admin</h2>
                <p>Control Panel</p>
            </div>
            <nav>
                <a href="javascript:void(0)" onclick="showSection(event, 'dashboard-container')" class="nav-btn active">Dashboard</a>
                <a href="javascript:void(0)" onclick="showSection(event, 'logs-container')" class="nav-btn">Activity logs</a>
                <a href="javascript:void(0)" onclick="showSection(event, 'new-admin-container')" class="nav-btn">Add New Admin</a>
                <a href="../../index.php?action=logout" class="nav-btn">Logout</a>
            </nav>
        </aside>
       <section id="dashboard-container" class="tab-content" >
            
       </section>
       <section id="logs-container" class="tab-content hidden">
           <div class="logs-controls">
                <h3>System Activity Logs</h3>
                <input type="text" id="logSearch" placeholder="Search logs...">
            </div>
            <div>
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead style=" position: sticky;">
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Details</th>
                            </tr>
                    </thead>
                    <tbody>
                        <?php if($log_result && $log_result->num_rows > 0):?>
                            <?php while($logs = $log_result->fetch_assoc()):?>
                                <tr>
                                    <td><small><?php echo date('M d, H:i',strtotime($logs['created_at']));?></small></td>
                                    <td><?php echo htmlspecialchars($logs['USER_EMAIL']);?></td>
                                    <td><strong><?php echo htmlspecialchars($logs['action_type']);?></strong></td>
                                    <td><?php echo htmlspecialchars($logs['details']);?></td>
                                </tr>
                            <?php endwhile;?>
                        <?php else: ?>
                            <tr>
                                <td colspan = "4">
                                    No recent activity.
                                </td>
                            </tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>
            </div>
        </section>
        <section id="new-admin-container" class="tab-content hidden">
            <div class="new-admin-control">
                <div class="control-header">
                    <h2>Add New Admin</h2>
                    <p>Fill out the details to create a new administrative account.</p>
                </div>
                <form action="add-admin.php" method="post" class="admin-form">
                    <div class="form-grid">

                        <div class="input-group">
                            <input type="text" name="fName" id="fName" placeholder = "Add Admin Fistname" value = "<?php echo htmlspecialchars($fName ?? ''); ?>" required>
                            <?php if(isset($errors['fName'])) :?>
                                <span class="error"><?php echo $errors['fName'];?></span>
                            <?php endif;?>
                        </div>
                        <div class="input-group">
                            <input type="text" name="lName" id="lName" placeholder = "Add Admin Lastname" value = "<?php echo htmlspecialchars($lName ?? ''); ?>" required>
                            <?php if(isset($errors['lName'])) :?>
                                <span class="error"><?php echo $errors['lName'];?></span>
                            <?php endif;?>
                        </div>

                        <div class="input-group">
                            <input type="email" name="email" id="email" placeholder = "Add Admin Email" value = "<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            <?php if(isset($errors['email'])) :?>
                                <span class="error"><?php echo $errors['email'];?></span>
                            <?php endif;?>
                        </div>

                        <div class="input-group">
                            <input type="password" name="password" id="password" placeholder = "Temporary Password" required>
                            <?php if(isset($errors['password'])) :?>
                                <span class="error"><?php echo $errors['password'];?></span>
                            <?php endif;?>
                        </div>

                        <input type="submit" value="Create Admin" class="submit-btn">
                    </div>
                </form>
            </div>

            <div class="search-wrapper">
                <div class="admin-search-box">
                    <input type="text" id="search" placeholder="Search by email...">
                    <input type="button" id="clear-btn" value="Clear">
                </div>
            </div>

            <div class="table-wrapper">
                <table class="admin-table" id="adminlist">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result && $result->num_rows > 0):?>
                            <?php
                                while($user = $result->fetch_assoc()):
                                    $hashed_uid = urlencode(base64_encode($user['USER_ID'])); 
                            ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($user['USER_EMAIL']); ?>
                                </td>
                                <td>
                                    <span class="status-pill">
                                        <?php echo htmlspecialchars($user['USER_ROLE']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btn">
                                    <a href="reset-password.php?id=<?php echo $hashed_uid; ?>">Reset Password</a>
                                    <a href="archive-user.php?id=<?php echo $hashed_uid; ?>" onclick = "return confirm('Archive this Admin?')">Remove (Archive)</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr class="empty-box">
                                <td colspan="3">No active admin found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
<script src="script.js"></script>
</body>
</html>