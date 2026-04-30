<?php
    session_start();
    require('../../db-connect.php');

    if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] === 'Regular' ){
        header('Location: ../../index.php?error=unauthorized');
        exit;
    }

    $sql = "SELECT ITEM_ID, ITEM_NAME,ITEM_DESCRIPTION, ITEM_PRICE, ITEM_IMAGE, ITEM_STATUS FROM items";
    $res_update = $conn->query($sql. " WHERE ITEM_STATUS != 'Archived' AND ITEM_STATUS != 'Pending'");
    $res_archive = $conn->query($sql . " WHERE ITEM_STATUS = 'Archived'");
    $res_pending = $conn->query($sql . " WHERE ITEM_STATUS = 'Pending'");

    function renderTable($sectionId, $title, $data, $emptyMsg, $mode){ ?>
        <div id="<?php echo $sectionId; ?>" class="tab-content hidden">
                <div class="content-header">
                    <h2><?php echo $title; ?></h2>
                </div>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Image</th>
                                <th class="action-cells">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($data->num_rows > 0):?>
                                <?php
                                    while($item = $data->fetch_assoc()):
                                    $hashed_id =urlencode(base64_encode($item['ITEM_ID']));
                                ?>
                                <tr>
                                    <td>
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($item['ITEM_NAME']); ?>" readonly class="edit-input name-in">
                                    </td>
                                    <td>
                                        <span class="status-pill <?php echo htmlspecialchars($item['ITEM_STATUS']); ?>">
                                            <?php echo htmlspecialchars($item['ITEM_STATUS']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <textarea readonly name="desc" class="edit-input desc-in"><?php echo htmlspecialchars($item['ITEM_DESCRIPTION']); ?></textarea>
                                    </td>
                                    <td>
                                        <span style="display:ruby-text;">
                                           ₱<input type="number" steps="0.01" name="price" value="<?php echo htmlspecialchars($item['ITEM_PRICE']); ?>" readonly class="edit-input price-in">
                                        </span>
                                    </td>
                                    <td>
                                        <div class="image-wrapper">
                                            <img src="../../uploads/<?php echo htmlspecialchars($item['ITEM_IMAGE']); ?>" alt="Item Image" class="table-img item-image-display">
                                            <label for="file<?php echo $item['ITEM_ID'];?>" class="file-label hidden">
                                                <span class="placeholder">&#128462; Upload Image</span>
                                                <span class="file-name"></span>
                                            </label>
                                            <input type="file" name="file" class="edit-image-input hidden" id="file<?php echo $item['ITEM_ID'];?>">
                                        </div>
                                    </td>
                                    <td class="action-cells">
                                        <a href="javascript:void(0)" onclick = "toggleEdit(this)" class="btn btn-approve" id="prod-action" data-id="<?php echo urlencode(base64_encode($item['ITEM_ID'])); ?>">Edit</a>
                                        <?php if($mode === 'archive'): ?>
                                            <a href="javascript:void(0)" class="btn btn-approve" onclick="performAction('restore-item.php?id=<?php echo $hashed_id; ?>', this)">Restore</a>
                                        <?php elseif($mode === 'pending'): ?>
                                            <a href="javascript:void(0)" class="btn btn-approve" onclick="performAction('approve-item.php?id=<?php echo $hashed_id; ?>', this)">Approve</a>
                                            <a href="javascript:void(0)" class="btn btn-archive" onclick="performAction('archive-item.php?id=<?php echo $hashed_id; ?>', this)">Archive</a>
                                        <?php else: ?>
                                            <a href="javascript:void(0)" class="btn btn-archive" onclick="performAction('archive-item.php?id=<?php echo $hashed_id; ?>', this)">Archive</a>
                                            <?php endif; ?>
                                            <a href="javascript:void(0)" onclick = "handleViewCancel(this)" class = "btn btn-view-cancel">View</a>
                                    </td>
                                </tr>
                                <?php endwhile;?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <div class="empty-box">
                                            <p><?php echo $emptyMsg; ?></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
<?php }?>

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
                <button onclick="showSection(event, 'storeManagement')" class="nav-btn active">Manage Store</button>
                <button onclick="showSection(event, 'updateSection')" class="nav-btn">Update Product</button>
                <button onclick="showSection(event, 'pendingSection')" class="nav-btn ">Pending Items</button>
                <button onclick="showSection(event, 'archiveSection')" class="nav-btn">Archive Item</button>
                <button onclick="showSection(event, 'userSection')" class="nav-btn">Manage Users</button>
                <hr>
                <?php if($_SESSION['user_role'] === "Super Admin"):?>
                    <a href="../super-admin/super-admin-dashboard.php" class="nav-link">Super Admin</a>
                <?php endif;?>
                <a href="../../index.php" class="nav-link">Back to Store</a>
                <a href="../../index.php?action=logout" class="nav-link logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">

            <!--PENDING ITEM SECTION-->
            <?php renderTable('pendingSection', 'Pending Items', $res_pending, 'No pending items found.', 'pending'); ?>
            <!-- ARCHIVE SECTION -->
            <?php renderTable('archiveSection', 'Archived Items', $res_archive, 'No archived items found.', 'archive'); ?>
            <!-- UPDATE SECTION -->
             <?php renderTable('updateSection', 'Active Products', $res_update, 'No items found.', 'update'); ?>
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
                                    <a href="javascript:void(0)" onclick = "handleViewCancel(this)" class = "btn btn-view-cancel">View</a>
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

            <!-- Store Management Section -->
            <div id="storeManagement" class="tab-content">
                 <?php $sampleStore = "SELECT ITEM_ID, ITEM_NAME, ITEM_DESCRIPTION, ITEM_PRICE, ITEM_IMAGE, ITEM_STATUS FROM items";
                        $store = $conn->query($sampleStore);

                        $counts = [
                            'all' => 0,
                            'Approved' => 0,
                            'Pending' => 0,
                            'Archived' =>0
                            ];
                        $itemCount = [];
                        while($rows = $store->fetch_assoc()){
                            $itemCount[] = $rows;
                            $counts['all']++;
                            if(isset($counts[$rows['ITEM_STATUS']])){
                                $counts[$rows['ITEM_STATUS']]++;
                            }
                        }

                ?>
                <h1>Store Management</h1>
                <div class="dashboard">
                    <div class="dash-card allPRoducts" onclick ="filterStore('all')">
                        <h2>All Products</h2>
                        <p><?php echo $counts['all']; ?></p>
                    </div>
                    <div class="dash-card activeProducts" onclick ="filterStore('Approved')">
                        <h2>Active Products</h2>
                        <p><?php echo $counts['Approved']; ?></p>
                    </div>
                    <div class="dash-card pendingProducts" onclick ="filterStore('Pending')">
                        <h2>Pending Products</h2>
                        <p><?php echo $counts['Pending']; ?></p>
                    </div>
                    <div class="dash-card archivedProducts" onclick ="filterStore('Archived')">
                        <h2>Archive Products</h2>
                        <p><?php echo $counts['Archived']; ?></p>
                    </div>
                </div>
                <main class="product-grid">
                    <?php if(!empty($itemCount)):?>
                        <?php foreach($itemCount as $item): ?>
                            <div class="card" data-status = "<?php echo htmlspecialchars($item['ITEM_STATUS']); ?>">
                                <div class="status-badge <?php echo htmlspecialchars($item['ITEM_STATUS']);?>" id="status">
                                    <h3>Status: <?php echo htmlspecialchars($item['ITEM_STATUS']);?></h3>
                                </div>
                                <div class="item-image">
                                    <?php if(!empty($item['ITEM_IMAGE'])):?>
                                        <img src="../../uploads/<?php echo htmlspecialchars($item['ITEM_IMAGE']); ?>" alt="Product Image.">
                                    <?php else: ?>
                                        <div class="placeholder">No Image.</div>
                                    <?php endif; ?>
                                </div>

                                <h3><?php echo htmlspecialchars($item['ITEM_NAME']);?></h3>
                                                
                                <p class="price">
                                    &#8369; <?php echo number_format($item['ITEM_PRICE'], 2);?>
                                </p>

                                <p class="description">
                                    <?php echo htmlspecialchars($item['ITEM_DESCRIPTION']); ?>
                                </p>

                                <input type="button" class="view-details" 
                                    data-title="<?php echo htmlspecialchars($item['ITEM_NAME']); ?>"
                                    data-desc ="<?php echo htmlspecialchars($item['ITEM_DESCRIPTION']); ?>"
                                    value ="View Details">
                            </div>
                        <?php endforeach; ?>
                    <?php endif;?>
                </main>                
            </div>
        </main>
    </div>
    <div class="modal" id="descModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modalTitle"></h2>
            <hr>
            <p id="modalFullDesc"></p>
        </div>
    </div>
    <div id="toast" class="toast"></div>
</body>
<script src="script.js"></script>
    
</html>