<?php
    session_start();
    require('db-connect.php');

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");

    if (isset($_GET['action']) && $_GET['action'] === 'logout'){
        $_SESSION = array();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    $search  = $_GET['search'] ?? '';
    $searchItem = "%$search%";

    $sql = "SELECT ITEM_ID, ITEM_NAME, ITEM_DESCRIPTION, ITEM_PRICE, ITEM_IMAGE FROM items WHERE ITEM_STATUS = 'Approved' AND (ITEM_NAME LIKE ? OR ITEM_DESCRIPTION LIKE ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchItem,$searchItem);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style.css?v=1.1">
    <title>Document</title>
</head>
<body>
    <header>
    <h1>Welcome</h1>
    <div class="navbar">
        <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'Admin'):?>
            <a href="dashboard/admin/admin-dashboard.php">Admin</a>
        <?php endif; ?>
        <?php if (!isset($_SESSION['logged_in'])): ?>
            <a href="authorization/auth.php?mode=login">Log in</a>
            <a href="authorization/auth.php?mode=registration">Register</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['logged_in'])): ?>
            <a href="#">Profile</a>
            <a href="#">Cart</a>
            <a href="index.php?action=logout">Logout</a>
        <?php endif; ?>
    </div>
    </header>
    <section>
        <form action="index.php" method="get">
            <input type="text" name="search" id="search" placeholder = "Search Item" value = "<?php echo htmlspecialchars($search); ?>">
            <input type="submit" value="Search">
        </form>
    </section>

    <main class="product-grid">
        <?php if($result->num_rows > 0):?>
            <?php while($item = $result->fetch_assoc()): ?>
                <div class="card">

                    <div class="item-image">
                        <?php if(!empty($item['ITEM_IMAGE'])):?>
                            <img src="uploads/<?php echo htmlspecialchars($item['ITEM_IMAGE']); ?>" alt="Product Image.">
                        <?php else: ?>
                            <div class="placeholder">No Image.</div>
                        <?php endif; ?>
                    </div>

                    <h3><?php echo htmlspecialchars($item['ITEM_NAME']);?></h3>
                            
                    <p class="price">
                        <?php echo number_format($item['ITEM_PRICE'], 2);?>
                    </p>

                    <p class="description">
                        <?php echo htmlspecialchars($item['ITEM_DESCRIPTION']); ?>
                    </p>

                    <input type="button" class="view-details" 
                        data-title="<?php echo htmlspecialchars($item['ITEM_NAME']); ?>"
                        data-desc ="<?php echo htmlspecialchars($item['ITEM_DESCRIPTION']); ?>"
                        value ="View Details">
                        

                    <?php if(isset($_SESSION['logged_in'])):?>
                        <form action="user-action/cart.php" method="post">
                            <input type="hidden" name="item_id" value="<?php echo $item['ITEM_ID']; ?>">
                            <input type="submit" value="Add to Cart" class="btn-order">
                        </form>
                    <?php else: ?>
                        <a href="authorization/auth.php?mode=login">
                            <input type="button" value="Order now">
                        </a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No Items Found.</p>
        <?php endif;?>
    </main>
    <?php if(isset($_SESSION['logged_in']) && $_SESSION['user_role'] === 'Regular'): ?>
        <section class="user-action">
            <h3>Have a product suggestion?</h3>
            <input type="button" id="openSuggestModal" class="btn-suggest" value="Suggest new Item">
        </section>
    <?php endif; ?>

    <div class="modal" id="descModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modalTitle"></h2>
            <hr>
            <p id="modalFullDesc"></p>
        </div>
    </div>

    <div id="suggestModal" class="modal">
        <div class="modal-content">
            <span class="close-suggest">&times;</span>
            <h2 class="formTitle">Suggest a new Item</h2>
            <hr>
            <form action="user-action/suggest-item.php" method="post" enctype ="multipart/form-data">
               <div class="form-group">
                    <input type="text" name="item_name" id="item_name" required>
                    <label for="item_name" class="label">Product Name</label>
                    <?php if (isset($errors['name'])) echo"<p>".$errors['name']."</p>" ?>
                    <span class="underline"></span>
               </div>

                <div class="form-group">
                    <textarea name="item_description" id="item_description" required class="descArea"></textarea>
                    <label for="item_description"  class="label">Describe the product...</label>
                    <?php if (isset($errors['desc'])) echo"<p>".$errors['desc']."</p>" ?>
                    <span class="underline"></span>
                </div>
                
                <div class="form-file">
                    <label for="image" class="file-label">
                        <span class="placeholder">&#128462; Upload Sample Image</span>
                        <span id="file-name"></span>
                    </label>
                    <span class="underline"></span>
                    <input type="file" name="item_image" id="image" accept="image/*" required>
                    <?php if (isset($errors['image'])) echo"<p>".$errors['image']."</p>" ?>
                </div>
                <p class="optional">&#9432; Image is optional</p>

                <input type="submit"  class="btn-submit" value="Submit">
            </form>
        </div>
    </div>
</body>
<script src="src/script.js"></script>
</html>