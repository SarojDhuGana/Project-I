<?php
require_once ('../product/ProductManager.php');

$productManager = new ProductManager();

$product = $productManager->viewProductList();

$productDecode = json_decode($product, true);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="productlists.css">
</head>

<body>
    <div class="mainContainer">
        <h2>All Products</h2>
        <div class="headerContainer">
            <p class="check">Select all <input type="checkbox" id="selectAll"></p>
            <button type="submit">Delete Selected Items</button>
        </div>
        <div class="box">
            <?php
            if (!empty($productDecode) && $productDecode['success'] == true) {
                foreach ($productDecode['data'] as $product) { ?>
                    <div class="product">
                        <div class="productDetail">
                            <div class="checkBoxClass">
                                <input class="checkBox" name="user_ids[]"
                                    value="<?= htmlspecialchars($product['product_id']); ?>" type="checkbox">
                                <img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="Product Image">
                            </div>
                            <div class="description">
                                <p class="ellipsis"><?= htmlspecialchars($product['name']); ?></p>
                                <p>Rs. <?= number_format($product['price'], 2); ?></p>
                                <p>In Stock: <?= htmlspecialchars($product['quantity']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php }
            } else {
                echo $productDecode['message'];
            }
            ?>
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function () {
            var checkboxes = document.querySelectorAll('.checkBox');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
            }
        });
    </script>
</body>

</html>