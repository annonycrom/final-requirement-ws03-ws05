<?php
    session_start();
    require('db-connect.php');

    if (isset($_GET['action']) && $_GET['action'] === 'logout'){
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    $search  = $_GET['search'] ?? '';
    $searchItem = "%$search%";

    $sql = "SELECT ITEM_ID, ITEM_NAME, ITEM_DESCRIPTION FROM items WHERE ITEM_STATUS = 'Approved' AND (ITEM_NAME LIKE ? OR ITEM_DESCRIPTION LIKE ?)";

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
    <title>Document</title>
</head>
<body>
    <header>
    <h1>Welcome</h1>
    <div class="navbar">
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
                    <h3><?php echo htmlspecialchars($item['ITEM_NAME']);?></h3>
                    <p><?php echo htmlspecialchars($item['ITEM_DESCRIPTION']); ?></p>
                    <a href="authorization/auth.php?mode=login">
                        <input type="button" value="Order now">
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No Items Found.</p>
        <?php endif;?>
    </main>
    <?php if(isset($_SESSION['logged_in']) && $_SESSION['user_role'] === 'Regular'): ?>
        <section class="user-action">
            <h3>Have a product suggestion?</h3>
            <a href="user-action/suggest-item.php"><input type="button" value="Add new Item"></a>
        </section>
    <?php endif; ?>
</body>
</html>