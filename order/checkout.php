<?php
require_once ('Order.php');
require_once ('../product/ProductManager.php');
require_once ('../BuyerManager.php');
session_start();

if (!empty($_SESSION['buyer_id'])) {
    if (!empty($_GET)) {
        $cart_idFinal = isset($_GET['cart_id']) ? $_GET['cart_id'] : '';
        $product_idFinal = isset($_GET['product_id']) ? $_GET['product_id'] : '';

        $cart_idOrProduct_id = !empty($cart_idFinal) ? $cart_idFinal : $product_idFinal;

        // Retrieve buyer details
        $buyerManager = new BuyerManager();
        $buyerDetails = $buyerManager->getBuyerDetails($_SESSION['buyer_id']);
        $buyerDecode = json_decode($buyerDetails, true);

        echo ($buyerDecode['full_name']);

        if (!empty($product_idFinal)) {
            $orderInstance = new Order();
            $sellerDetailsFromProduct = $orderInstance->getSellerDetailsFromProductId($cart_idOrProduct_id); // to store seller id
            $sellerDetailsDecode = json_decode($sellerDetailsFromProduct, true);


            // Can be extract product id from cart id
            $productManager = new ProductManager();
            $productDetails = $productManager->getProductDetails($cart_idOrProduct_id);
            $productDecode = json_decode($productDetails, true);

        }

        if (!empty($cart_idFinal)) {
            // Retrieve cart details
            $orderInstance = new Order();
            $cartDetail = $orderInstance->cartDetails($cart_idOrProduct_id);
            $cartDecode = json_decode($cartDetail, true);

            if (is_array($cartDecode) && !empty($cartDecode)) {
                $p_id = '';
                foreach ($cartDecode as $cart) {
                    if (isset($cart['product_id'])) {
                        $p_id = $cart['product_id'];
                        break; // Exit loop after found product_id
                    }
                }
                if (!empty($p_id)) {
                    $orderInstance = new Order();
                    $sellerDetailsFromProduct = $orderInstance->getSellerDetailsFromProductId($p_id);
                    $sellerDetailsDecode = json_decode($sellerDetailsFromProduct, true);
                }
            }
        }
    } else {
        echo "Product id not found";
    }
} else {
    header('Location:../allUserLogin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Order Form</title>
    <link rel="stylesheet" href="checkout.css">
    <script src="../productIncrement.js"></script>
</head>
<style>
    button.disabled-button {
        background-color: #ccc;
        cursor: not-allowed;
    }
</style>

<body>
    <div class="container">
        <div class="title">
            <h2>Checkout shipping</h2>
        </div>
        <div class="d-flex">

            <form action="makeOrder.php" method="post" id="orderForm">
                <?php
                // For seller id
                foreach ($sellerDetailsDecode as $seller) { ?>
                    <input type="hidden" name="seller_id" value="<?= $seller['seller_id']; ?>">
                <?php }

                if (!empty($buyerDecode)) {
                    foreach ($buyerDecode as $buyer) { ?>
                        <label>
                            <input type="hidden" name="buyer_id" value="<?= $buyer['buyer_id']; ?>">
                            <span class="full_name">Name <span class="required">*</span></span>
                            <input type="text" name="full_name" id="full_name"
                                value="<?= htmlspecialchars($buyer['full_name'] ?? ''); ?>" readonly>
                        </label>
                        <label>
                            <span>Email <span class="required">*</span></span>
                            <input type="email" name="email" value="<?= htmlspecialchars($buyer['email'] ?? ''); ?>">
                        </label>
                        <label>
                            <span>Phone Number <span class="required">*</span></span>
                            <input type="text" name="phone_number" id="phone_number"
                                value="<?= htmlspecialchars($buyer['phone_number'] ?? ''); ?>" required>
                        </label>
                        <label>
                            <span class="fname">District<span class="required">*</span></span>
                            <input type="text" name="district" value="<?= htmlspecialchars($buyer['district'] ?? ''); ?>"
                                required>
                        </label>
                        <label>
                            <span>City / Town <span class="required">*</span></span>
                            <input type="text" name="city" value="<?= htmlspecialchars($buyer['city'] ?? ''); ?>">
                        </label>
                        <label>
                            <span>Street<span class="required">*</span></span>
                            <input type="text" name="street" value="<?= htmlspecialchars($buyer['street'] ?? ''); ?>"
                                placeholder="House number and street name">
                        </label>
                        <label>
                            <span>&nbsp;</span>
                            <input type="text" name="apartment" placeholder="Apartment, suite, unit etc. (optional)">
                        </label>
                        <label>
                            <span>State<span class="required">*</span></span>
                            <input type="text" name="state" value="<?= htmlspecialchars($buyer['state'] ?? ''); ?>">
                        </label>
                        <label>
                            <span>Postcode/ZIP <span class="required">*</span></span>
                            <input type="text" name="zip" value="<?= htmlspecialchars($buyer['zip'] ?? ''); ?>">
                        </label>

                    <?php }
                } else {
                    echo "<p>User data not found</p>";
                }

                $cartOutOfStock = false; // Initialize outOfStock variable
                
                if (!empty($cartDecode) && is_array($cartDecode)) {
                    foreach ($cartDecode as $index => $cart) {
                        if (is_array($cart) && !empty($cart)) {
                            // $outOfStock = ($cart['quantity'] ?? 0) == 0; // Check if product is out of stock
                            if ($cart['cart_quantity'] == 0) {
                                $cartOutOfStock = true;
                                echo $cartOutOfStock;
                            }
                            ?>
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($cart['product_id'] ?? ''); ?>">
                            <input type="hidden" name="cart_id" value="<?= htmlspecialchars($cart['cart_id'] ?? ''); ?>">
                            <input type="hidden" name="price" value="<?= htmlspecialchars($cart['price'] ?? ''); ?>">
                            <table>
                                <thead>
                                    <tr>
                                        <th colspan="6">Your order</th>
                                    </tr>
                                    <tr>
                                        <th>Product</th>
                                        <th>Unit price</th>
                                        <th>Ordered Quantity</th>
                                        <th>Total Payable</th>
                                        <th></th>
                                        <th>In Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= htmlspecialchars($cart['name'] ?? ''); ?></td>
                                        <td><?= htmlspecialchars($cart['price'] ?? ''); ?></td>

                                        <td><?= isset($cart['cart_quantity']) ? $cart['cart_quantity'] : ''; ?>
                                            <input type="hidden" name="quantity" id="hiddenQuantity"
                                                value="<?= isset($cart['cart_quantity']) ? $cart['cart_quantity'] : ''; ?>">
                                        </td>
                                        <td id="item-<?= $index ?>-total-payable">
                                            <?= htmlspecialchars($cart['total_amount'] ?? ''); ?>
                                            <input type="hidden" name="total_amount" value="<?= $cart['total_amount'] ?? ''; ?>">
                                        </td>
                                        <td></td>
                                        <td><?= $cart['quantity'] ?? ''; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php }
                    }
                } elseif (!empty($productDecode) && $productDecode['success'] == true) {
                    // Display product details if not found in cart
                    foreach ($productDecode['data'] as $product) {
                        $outOfStock = ($product['quantity'] ?? 0) == 0; // Check if product is out of stock
                        ?>
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id'] ?? ''); ?>">
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="6">Your order</th>
                                </tr>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit price</th>
                                    <th>Ordered Quantity</th>
                                    <th>Total Payable</th>
                                    <th></th>
                                    <th>In Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ellipsis"> <?= htmlspecialchars($product['name'] ?? ''); ?></td>
                                    <td>
                                        <?= htmlspecialchars($product['price'] ?? ''); ?>
                                        <input type="hidden" name="price" value="<?= htmlspecialchars($product['price']); ?>">
                                    </td>
                                    <td>
                                        <div class="quantity-input">
                                            <button type="button" class="decrementButton"
                                                onclick="updateQuantity(-1, <?= $product['price']; ?>, <?= htmlspecialchars($product['quantity']); ?>)">-</button>

                                            <input type="text" class="cartValue" name="quantity" value="1" readonly>

                                            <button type="button" class="incrementButton"
                                                onclick="updateQuantity(1, <?= $product['price']; ?>,<?= htmlspecialchars($product['quantity']); ?>)">+</button>
                                        </div>
                                        <input type="hidden" name="new_quantity" value="1"> <!-- Initial value 1 -->
                                    </td>
                                    <td>
                                        <span class="totalPayable"><?= htmlspecialchars($product['price'] ?? ''); ?></span>
                                        <input type="hidden" name="total_amount"
                                            value="<?= htmlspecialchars($product['price']); ?>">
                                    </td>
                                    <td></td>
                                    <td>
                                        <?= htmlspecialchars($product['quantity']); ?>
                                        <!-- Display stock quantity from database -->
                                        <input type="hidden" name="stock_quantity"
                                            value="<?= htmlspecialchars($product['quantity']); ?>">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <?php }
                } else {
                    echo $productDecode['message'];
                }
                ?>
                <div class="paymentMethod">
                    <label>Select Payment Method</label>
                    <div class="cashOndelivery">
                        <label for="cod">Cash on delivery</label>
                        <input type="checkbox" id="cod" checked class="checkMe">
                    </div>
                </div>
                <button type="button" onclick="validateForm()" id="placeOrderButton"
                    class="<?= $outOfStock ? 'disabled-button' : ''; ?>" <?= $outOfStock ? 'disabled' : ''; ?>>Place
                    Order</button>
            </form>
        </div>
    </div>
    <script>
        var outOfStock = <?= json_encode($outOfStock); ?>;

        function updateQuantity(change, price, maxQuantity) {
            var quantityInput = document.querySelector('.cartValue');
            var currentQuantity = parseInt(quantityInput.value);
            var newQuantity = currentQuantity + change;

            if (newQuantity < 1 || newQuantity > maxQuantity) {
                return; // Don't allow quantity to be less than 1 or exceed maxQuantity
            }

            quantityInput.value = newQuantity;

            // Update hidden input field for new quantity
            document.querySelector('input[name="new_quantity"]').value = newQuantity;

            // Calculate and update hidden input field for updated total amount
            var updatedTotalAmount = newQuantity * price;
            document.querySelector('input[name="total_amount"]').value = updatedTotalAmount;

            // Update total payable displayed on the page
            var totalPayableSpan = document.querySelector('.totalPayable');
            totalPayableSpan.textContent = updatedTotalAmount;
        }

        function validateForm() {
            var checkBox = document.querySelector('.checkMe');
            var placeOrderButton = document.getElementById('placeOrderButton');
            if (!checkBox.checked) {
                alert('Please select Cash on delivery to proceed.');
            } else if (outOfStock) {
                alert('Product is out of stock. Cannot proceed with the order.');
                placeOrderButton.disabled = true;
                placeOrderButton.classList.add('disabled-button');
            } else {
                var orderForm = document.getElementById('orderForm');
                orderForm.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (outOfStock) {
                var placeOrderButton = document.getElementById('placeOrderButton');
                placeOrderButton.disabled = true;
                placeOrderButton.classList.add('disabled-button');
            }
        });
    </script>
</body>

</html>