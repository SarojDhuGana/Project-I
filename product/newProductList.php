<?php
require_once('ProductManager.php');
$productManager = new ProductManager();
if (!empty($_GET)) { // Check if it's a GET request
    $id = $_GET['id'];
    $response = array();
    $data = $productManager->getProductDetails($id);
    // Decode the JSON response
    $productData = json_decode($data, true);

}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<style>
    * {
        padding: 0;
        margin: 0;
        position: relative;
        box-sizing: border-box;
    }

    .listing-section,
    .cart-section {
        width: 100%;
        float: left;
        padding: 1%;
        border-bottom: 0.01em solid #dddbdb;
    }

    .product {
        float: left;
        width: 23%;
        border-radius: 2%;
        margin: 1%;
    }

    .product:hover {
        box-shadow: 1.5px 1.5px 2.5px 3px rgba(0, 0, 0, 0.4);
        -webkit-box-shadow: 1.5px 1.5px 2.5px 3px rgba(0, 0, 0, 0.4);
        -moz-box-shadow: 1.5px 1.5px 2.5px 3px rgba(0, 0, 0, 0.4);
    }

    .image-box {
        width: 100%;
        overflow: hidden;
        border-radius: 2% 2% 0 0;
    }

    .images {
        height: 15em;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        border-radius: 2% 2% 0 0;
        transition: all 1s ease;
        -moz-transition: all 1s ease;
        -ms-transition: all 1s ease;
        -webkit-transition: all 1s ease;
        -o-transition: all 1s ease;
    }

    .images:hover {
        transform: scale(1.2);
        overflow: hidden;
        border-radius: 2%;
    }

    .text-box {
        display: flex;
        flex-direction: column;
        width: 100%;
        float: left;
        border: 0.01em solid #dddbdb;
        border-radius: 0 0 2% 2%;
        padding: 1em;
    }

    h2,
    h3 {
        float: left;
        font-family: 'Roboto', sans-serif;
        font-weight: 400;
        font-size: 1em;
        text-transform: uppercase;
        margin: 0.2em auto;
    }

    .item,
    .price {
        clear: left;
        width: 100%;
        text-align: center;
        text-overflow: ellipsis;
        overflow: hidden;
        margin: 0;
        white-space: nowrap;
    }

    .price {
        color: Grey;
    }

    .description,
    label,
    button,
    input {
        float: left;
        clear: left;
        width: 100%;
        font-family: 'Roboto', sans-serif;
        font-weight: 300;
        font-size: 1em;
        text-align: center;
        margin: 0.2em auto;
    }

    input:focus {
        outline-color: #fdf;
    }

    label {
        width: 60%;
    }

    .text-box input {
        width: 15%;
        clear: none;
    }

    .text-box button {
        margin-top: 1em;
    }

    button {
        padding: 2%;
        background-color: #dfd;
        border: none;
        border-radius: 2%;
    }

    button:hover {
        bottom: 0.1em;
    }

    button:focus {
        outline: 0;
    }

    button:active {
        bottom: 0;
        background-color: #fdf;
    }

    .table-heading,
    .table-content {
        width: 75%;
        margin: 1% 12.25%;
        float: left;
        background-color: #dfd;
    }

    .table-heading h2 {
        padding: 1%;
        margin: 0;
        text-align: center;
    }

    .cart-product {
        width: 50%;
        float: left;
    }

    .cart-price {
        width: 15%;
        float: left;
    }

    .cart-quantity {
        width: 10%;
        float: left;
    }

    .cart-total {
        width: 25%;
        float: left;
    }

    .cart-image-box {
        width: 30%;
        overflow: hidden;
        border-radius: 2%;
        float: left;
        margin: 1%;
    }

    .cart-images {
        height: 7em;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
    }

    .cart-item {
        width: 20%;
        float: left;
        margin: 3.2em 1%;
        text-align: center;
    }

    .cart-description {
        width: 53%;
        float: left;
        margin: 3.2em 1%;
        font-family: 'Roboto', sans-serif;
        font-weight: 300;
        font-size: 1em;
        text-align: center;
    }

    .cart-price h3,
    .cart-total h3 {
        margin: 3.2em 5% 3.2em 20%;
        width: 60%;
    }

    .cart-quantity input {
        margin: 3.2em 1%;
        border: none;
    }

    .remove {
        width: 10%;
        margin: 1px;
        float: right;
        clear: right;
    }

    .chat {
        width: 20%;
        background-color: #dfd;
        margin: 1% 1% 1% 12.25%;
        float: left;
        height: 6em;
    }

    .chat input {
        width: 60%;
        border: none;
        margin: 12.75% 5%;
        padding: 1%;
    }

    .chat button {
        width: 25%;
        float: left;
        clear: right;
        margin: 12% 5% 12% 0;
    }

    .keep-shopping {
        width: 15%;
        height: 6em;
        float: left;
        margin: 1% auto;
        padding: 1%;
        background-color: #dfd;
    }

    .keep-shopping button {
        text-transform: uppercase;
        margin: 12% auto;

    }

    .checkout {
        width: 37.25%;
        margin: 1% 12.75% 1% 1%;
        float: right;
        background-color: #dfd;
        height: 6em;
    }

    .checkout button {
        width: 30%;
        clear: none;
        margin: 5.4% 0 5.4% 5.5%;
        text-transform: uppercase;
    }

    .final-cart-total {
        width: 15%;
        float: right;
        margin: 7%;
        background-color: White;
    }

    .final-cart-total h3 {
        color: Black;
    }

    .incrementButton {
        width: 25px;
        height: 25px;
    }

    .decrementButton {
        width: 25px;
        height: 25px;
    }

    .quantity-input {
        display: flex;
        justify-content: center;
        align-items: center;


    }

    .cartValue {
        /* width: 25px; */
    }

    .wrapper {
        display: flex;
        align-items: center;
        justify-content: center;

    }
</style>

<body>
    <div class="listing-section">
        <?php foreach ($productData as $index => $product) { ?>
            <div class="product">
                <div class="image-box">
                    <a href="product_details.php?id=<?= $product['product_id'] ?>">
                        <img class="images" src="../images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                    </a>
                </div>
                <div class="text-box">
                    <div class="mainWrapper">
                        <h2 class="item">
                            <?= $product['name']; ?>
                        </h2>
                        <h3 class="price">
                            Rs.
                            <?= $product['price']; ?>
                        </h3>
                        <!-- <p class="description">A bag of delicious oranges!</p> -->
                        <label for="item-<?= $index ?>-quantity">In stock:
                            <?= $product['quantity']; ?>
                        </label>
                    </div>
                    <div class="wrapper">
                        <label for="item-<?= $index ?>-quantity">Quantity:</label>
                        <div class="quantity-input">
                            <button type="button" class="incrementButton"
                                onclick="decreaseQuantity('item-<?= $index ?>-quantity')">-</button>
                            <input type="text" class="cartValue" name="item-<?= $index ?>-quantity"
                                id="item-<?= $index ?>-quantity" value="1" readonly>
                            <button type="button" class="decrementButton"
                                onclick="increaseQuantity('item-<?= $index ?>-quantity',<?= $product['quantity'] ?>)">+</button>
                        </div>
                    </div>
                    <button type="button" name="item-<?= $index ?>-button" id="item-<?= $index ?>-button">Add to
                        Cart</button>
                </div>
            </div>
        <?php } ?>
    </div>
    <script>
        function increaseQuantity(inputId, stockValue) {
            var input = document.getElementById(inputId);
            var currentValue = parseInt(input.value);

            if (currentValue < stockValue) {
                input.value = currentValue + 1;
            }
            // input.value = parseInt(input.value) + 1;
        }

        function decreaseQuantity(inputId) {
            var input = document.getElementById(inputId);
            var value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
            }
        }
    </script>
</body>