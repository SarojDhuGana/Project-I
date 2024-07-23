<?php
require_once ('SellerManager.php');
require_once ('../admin/category/CategoryManager.php');
session_start();
$sellerManager = new SellerManager();

if (!empty($_SESSION['seller_id'])) {

    // for displaying category
    $categoryManager = new CategoryManager();
    $categoryResponse = $categoryManager->list();
    $categoryDecode = json_decode($categoryResponse, true);



    $seller_id = $_SESSION['seller_id'];
    $response = $sellerManager->sellerDetails($seller_id);
    $responseArray = json_decode($response, true);

} else {
    echo "Seller ID not found in session.";
    header('Location: ../allUserLogin.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="sellerDashboard.css">
</head>

<body>
    <?php require_once ('sellerNavigation.php'); ?>
    <div class="container">
        <form id="contact" action="../product/productStore.php" method="post" enctype="multipart/form-data">
            <h3>Add your Product</h3>
            <fieldset>
                <h4>Select Category</h4>
                <select name="category_id" id="category_id">
                    <?php foreach ($categoryDecode as $category) { ?>
                        <option value="<?= $category['category_id']; ?>" data-name="<?= $category['name']; ?>">
                            <?= $category['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </fieldset>
            <fieldset>
                <input type="hidden" name="seller_id" id="seller_id" value="<?= $_SESSION['seller_id']; ?>">
            </fieldset>
            <fieldset>
                <input placeholder="Product name" type="text" name="name" id="name" tabindex="1" autofocus>
            </fieldset>
            <fieldset>
                <input placeholder="Price" type="number" name="price" id="price" tabindex="2">
            </fieldset>
            <fieldset>
                <input placeholder="Total stock" type="number" name="quantity" id="quantity" tabindex="3" min="1">
            </fieldset>
            <fieldset>
                <textarea placeholder="Description" name="description" id="description" tabindex="5"></textarea>
            </fieldset>
            <fieldset>
                <input type="file" name="image" id="image" tabindex="4">
            </fieldset>
            <fieldset>
                <button name="submit" type="submit" id="contact-submit" data-submit="...Sending">Submit</button>
            </fieldset>
        </form>
    </div>

    <script>
        const categoryPriceLimits = {
            'electronics': { min: 100, max: 500000 },
            'mobile phones': { min: 100, max: 500000 },
            'clothing': { min: 20, max: 100000 },
            'furniture': { min: 10, max: 300000 },
            'decor': { min: 10, max: 300000 }
        };

        function getPriceLimits(categoryName) {
            for (const key in categoryPriceLimits) {
                if (categoryPriceLimits.hasOwnProperty(key)) {
                    const aliases = key.split(',').map(alias => alias.trim().toLowerCase());
                    if (aliases.includes(categoryName.toLowerCase())) {
                        return categoryPriceLimits[key];
                    }
                }
            }
            return { min: 0, max: 5000000 };
        }

        document.getElementById('quantity').addEventListener('input', function (event) {
            if (this.value < 1) {
                this.value = '';
            }
        });

        document.getElementById('price').addEventListener('input', function (event) {
            const categorySelect = document.getElementById('category_id');
            const categoryName = categorySelect.options[categorySelect.selectedIndex].getAttribute('data-name').toLowerCase();
            const limits = getPriceLimits(categoryName);

            if (this.value <= 0) {
                this.value = '';
                return;
            }

            if (this.value > limits.max) {
                this.value = limits.max;
                alert(`Price for ${categoryName} cannot exceed ${limits.max}`);
            }
        });

        document.getElementById('contact').addEventListener('submit', function (event) {
            const categorySelect = document.getElementById('category_id');
            const categoryName = categorySelect.options[categorySelect.selectedIndex].getAttribute('data-name').toLowerCase();
            const name = document.getElementById('name').value;
            const price = document.getElementById('price').value;
            const quantity = document.getElementById('quantity').value;
            const description = document.getElementById('description').value;
            const limits = getPriceLimits(categoryName);

            if (!name) {
                alert('Product name is required');
                event.preventDefault();
                return;
            }

            if (!price || price <= 0) {
                alert('Price is required and must be greater than zero');
                event.preventDefault();
                return;
            }

            if (!quantity || quantity < 1) {
                alert('Quantity is required and must be at least 1');
                event.preventDefault();
                return;
            }

            if (price < limits.min) {
                alert(`Price for ${categoryName} must be at least ${limits.min}`);
                event.preventDefault();
                return;
            }

            if (price > 5000000) {
                alert('Price cannot exceed 5000000');
                event.preventDefault();
                return;
            }

            // Allow form submission
        });

        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                navItems.forEach(item => item.classList.remove('active'));
                item.classList.add('active');
            });
        });
    </script>
</body>

</html>