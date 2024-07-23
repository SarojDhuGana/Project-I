<style>
    img{
        height: 30px;
        margin-top: -10px;
    }
</style>
<!-------------------------------- header section start--------------------------->
<div class="navContainer">
    <div class="hamburger" id="hamburger">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <div class="mainNav" id="mainNav">
        <ul class="nav-items">
            <li class="nav-item ">
                <a href="">
                    <img src="sysImage/ttitle.png" alt="">
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="seller/sellerRegister.php">Became a seller</a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="./">Dashboard</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="allUserLogin.php">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="buyerRegister.php">Sign Up</a>
            </li>

        </ul>
    </div>
    <div class="navbar" id="searchBox">
        <label for="">Filter By:</label>
        <select name="categorySelect" id="categorySelect">
            <option value="">
                <b>Categories</b>
            </option>
            <?php foreach ($resultDecode as $category) { ?>
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
<script>
    // Get the current page URL
    var currentUrl = window.location.href;

    // Select all navigation links
    var navLinks = document.querySelectorAll('.nav-item a');

    // Loop through each navigation link
    navLinks.forEach(function(navLink) {
        // Compare the current URL with the href attribute of the link
        if (navLink.href === currentUrl) {
            // Add 'active' class to the parent 'li' element
            navLink.parentElement.classList.add('active');
        }
    });
</script>
