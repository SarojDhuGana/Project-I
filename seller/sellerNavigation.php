<nav class="navbar">
    <?php
    if (!empty($responseArray) && $responseArray['success'] !== true) {
        foreach ($responseArray as $seller) { ?>
            <a class="navbar-brand Username" href="sellerProfile.php"><?= $seller['company_name']; ?></a>
        <?php }
    } ?>
    <ul class="navbar-nav">
        <li class="nav-item active">
            <a class="nav-link" href="./">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="productList.php?seller_id=<?= $_SESSION['seller_id']; ?>">Products</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="sellerOrderList.php">Orders</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="sellerProfile.php">Profile</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
        </li>
    </ul>
</nav>