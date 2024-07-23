<?php
require_once('ProductManager.php');
$productManager = new ProductManager();
session_start();

// Proceed only if session buyer_id is set
if (isset($_SESSION['buyer_id'])) {
    $buyerId = $_SESSION['buyer_id'];
} else {
    $_SESSION['buyer_id'] = null; // Set buyerId to null if session buyer_id is not set
}

if (!empty($_GET['cat_id'])) {
    $categoryId = $_GET['cat_id'];
    $response = array();
    $result = $productManager->displayProductByCategory($categoryId);
    $resultDecode = json_decode($result, true);
    if ($resultDecode && isset($resultDecode['success']) && $resultDecode['success'] === false) {
        // If no products are found, display a message
        echo "<p>{$resultDecode['message']}</p>";
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="categoryProduct.css">
            <title>Document</title>
            <style>
                .disabled-button {
                    background-color: grey;
                    cursor: not-allowed;
                }

                .quantity-input button[disabled],
                .add-to-cart-button[disabled] {
                    background-color: grey;
                    cursor: not-allowed;
                }
            </style>
        </head>
        <style>
            #searchBox {
                /* display: none; */
            }
        </style>

        <body>

        <div class="listing-section">
    <?php foreach ($resultDecode as $index => $product) {
        $imagePath = "../images/" . $product["image"];
        $imageSrc = (!empty($product["image"]) && file_exists($imagePath)) ? $imagePath : "../images/defaultProduct.png";
        ?>
        <div class="product">
            <div class="image-box">
                <a href="product_details.php?product_id=<?= $product['product_id'] ?>">
                    <img class="images" src="<?= $imageSrc ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                </a>
            </div>
            <div class="text-box">
                <div class="mainWrapper">
                    <h2 class="item ellipsis"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <h3 class="price">Rs. <?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <label for="item-<?= $index ?>-quantity">In stock: <?= htmlspecialchars($product['quantity'], ENT_QUOTES, 'UTF-8'); ?></label>
                </div>
                <div class="wrapper">
                    <label for="item-<?= $index ?>-quantity">Quantity:</label>
                    <div class="quantity-input">
                        <button type="button" class="incrementButton" onclick="decreaseQuantity('item-<?= $index ?>-quantity')" <?= $product['quantity'] == 0 ? 'disabled' : '' ?>>-</button>
                        <input type="text" class="cartValue" name="products[<?= $index ?>][quantity]" id="item-<?= $index ?>-quantity" value="1" readonly>
                        <button type="button" class="decrementButton" onclick="increaseQuantity('item-<?= $index ?>-quantity', <?= $product['quantity']; ?>)" <?= $product['quantity'] == 0 ? 'disabled' : '' ?>>+</button>
                    </div>
                </div>
                <form action="../cart/addCart.php" method="post">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="name" value="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="price" value="<?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="quantity" id="selected-item-<?= $index ?>-quantity" value="1">
                    <input type="hidden" name="buyer_id" value="<?= htmlspecialchars($_SESSION['buyer_id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="add-to-cart-button" <?= $product['quantity'] == 0 ? 'disabled' : '' ?>>Add to Cart</button>
                </form>
            </div>
        </div>
    <?php } ?>
</div>

        </body>
        </html>
    <?php } ?>
<?php } ?>

<script>
    function increaseQuantity(inputId, stockValue) {
        var input = document.getElementById(inputId);
        var currentValue = parseInt(input.value);
        if (currentValue < stockValue) {
            input.value = currentValue + 1;
            document.getElementById('selected-' + inputId).value = currentValue + 1;
        }
    }

    function decreaseQuantity(inputId) {
        var input = document.getElementById(inputId);
        var value = parseInt(input.value);
        if (value > 1) {
            input.value = value - 1;
            document.getElementById('selected-' + inputId).value = value - 1;
        }
    }
</script>
