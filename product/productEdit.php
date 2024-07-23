<?php
require_once ('ProductManager.php');
require_once ('../admin/category/CategoryManager.php');

$categoryManager = new CategoryManager();
$category = $categoryManager->list();
$categoryDecode = json_decode($category, true);


if (!empty($_GET['pid'])) {
    $product_id = $_GET['pid'];
    $productManager = new ProductManager();
    $productDetails = $productManager->editProduct($product_id);
    $productDecode = json_decode($productDetails, true);

} else {
    header('Location:../seller/productList.php');
}

?>

<link rel="stylesheet" href="productEdit.css">
<style>
    img {
        height: 100px;
    }
</style>
<div class="container">
    <form id="contact" action="productUpdate.php" method="post" enctype="multipart/form-data">
        <h3>Edit Product </h3>
        <?php foreach ($productDecode as $product): ?>
            <fieldset>Select Category
                <select name="category_id" id="category_id">
                    <?php foreach ($categoryDecode as $category) { ?>
                        <?php $selected = ($category['category_id'] == $product['category_id']) ? "selected" : ""; ?>
                        <option value="<?= $category['category_id']; ?>" <?= $selected; ?> data-name="<?= $category['name']; ?>">
                            <?= $category['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </fieldset>

            <fieldset>
                <input type="hidden" value="<?= $product['product_id']; ?>" name="product_id" tabindex="1" required
                    autofocus>
            </fieldset>
            <fieldset>
                <label for="name">Product Name</label>
                <input placeholder="Product name" type="text" value="<?= $product['name']; ?>" id="name" name="name"
                    tabindex="1" autofocus>
            </fieldset>
            <fieldset>
                <label for="price">Price</label>
                <input placeholder="Price" type="number" value="<?= $product['price']; ?>" id="price" name="price"
                    tabindex="2" required>
            </fieldset>
            <fieldset>
                <label for="quantity">In stock</label>
                <input placeholder="IN STOCK" type="number" value="<?= $product['quantity']; ?>" id="quantity"
                    name="quantity" tabindex="3" required>
            </fieldset>
            <fieldset>
                <label for="description">Description</label>
                <textarea placeholder="Description" name="description"
                    tabindex="5"><?= $product['description']; ?></textarea>
            </fieldset>
            <fieldset>
                <label for="current_image">Current Image</label>
                <?php if (!empty($product['image'])): ?>
                    <div>
                        <img src="../images/<?= $product['image']; ?>" alt="<?= $product['name']; ?>" style="max-width: 200px;">
                        <input type="hidden" name="current_image" value="<?= $product['image']; ?>">
                    </div>
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
            </fieldset>
            <fieldset>
                <label for="image">Replace Image</label>
                <input type="file" name="image" id="image" tabindex="4">
            </fieldset>
            <fieldset>
                <button name="submit" type="submit" id="contact-submit" data-submit="...Sending">Submit</button>
            </fieldset>
        <?php endforeach ?>
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

    // document.getElementById('quantity').addEventListener('input', function (event) {
    //     if (this.value < 1) {
    //         this.value = '';
    //     }
    // });

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
        if (quantity == 0) {

            alert('Quantity is required and must be at least 1');
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