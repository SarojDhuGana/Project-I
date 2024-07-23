<div class="navContainer">
    <div class="hamburger" id="hamburger">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <div class="mainNav" id="mainNav">
        <li class="nav-item">
            <?php
            if (!empty($responseArray) && $responseArray['success'] !== false) {
                foreach ($responseArray as $user) { ?>
                    <a href="buyerProfile.php" onclick="window.reload()" class="title"
                        style="text-decoration:none;"><?= $user['full_name']; ?></a>
                <?php }
            } ?>
        </li>
        <ul class="nav-items">
            <li class="nav-item active">
                <a class="nav-link dashboard" href="./bdashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cart/showCarts.php">My Carts</a>
            </li>
            <li class="nav-item">
                <!-- <a class="nav-link" href="buyerOrder.php?buyer_id=<?= $_SESSION['buyer_id']; ?>">My Order</a> -->
                <a class="nav-link" href="buyerOrderList.php">My Order</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="buyerProfile.php">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>

    <div class="navbar">
        <label for="">Filter By:</label>
        <select name="categorySelect" id="categorySelect">
            <option value="">
                <b>Categories</b>
            </option>

            <?php foreach ($categoryDecode as $category) { ?>
                <option value="product/categoryProduct.php?cat_id=<?= $category['category_id']; ?>">
                    <?= $category['name']; ?>
                </option>
            <?php } ?>
        </select>
        <form action="searchProduct.php" method="get" id="searchForm">
            <input type="text" name="name" id="searchInput" placeholder="Search" autocomplete="off">
            <div class="search-history" id="searchHistoryContainer">
                <div class="clear-history" id="clearHistory">Clear History</div>
            </div>
        </form>
    </div>
</div>