<?php
require_once ('CartManager.php');
require_once ('../BuyerManager.php');

$cart = new CartManager();
session_start();

if (isset($_SESSION['buyer_id'])) {
    $buyer_id = $_SESSION['buyer_id'];
    $product = $cart->viewCart($buyer_id);
    $data = json_decode($product, true);
    if ($data['success']) {
        // Reverse the order of products array to display the last added products first
        $productData = array_reverse($data['data']);
    } else {
        $error_message = $data['message'];
    }
} else {
    header('Location: ../allUserLogin.php');
    exit();
}



// for buyer
$buyerManager = new BuyerManager();
if (!empty($_SESSION['buyer_id'])) {
    $buyer_id = $_SESSION['buyer_id'];
    $response = $buyerManager->getBuyerDetails($buyer_id);
    $responseArray = json_decode($response, true);
} else {
    header('Location:allUserLogin.php');
    exit(); // Exit after redirect
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- <link rel="stylesheet" href="../admin/productlists.css">     -->
    <!-- <link rel="stylesheet" href="../style/customDelete.css"> -->
    <link rel="stylesheet" href="showCarts.css">
    <link rel="stylesheet" href="../bdashboard.css">
</head>

<body>

    <div class="navigationBarCart">
        <?php require_once ('CartNavigation.php'); ?>
        
    </div>
    <div class="mainContainer">

        <div class="message-container">
            <?php
            if (isset($_SESSION['success_message'])) {
                echo "<p id='responseMessage' class='message success'>{$_SESSION['success_message']}</p>";
                unset($_SESSION['success_message']);
            } elseif (isset($_SESSION['error_message'])) {
                echo "<p id='responseMessage' class='message error'>{$_SESSION['error_message']}</p>";
                unset($_SESSION['error_message']);
            } else {
                echo "<p id='responseMessage' class='message hidden'></p>"; // Placeholder to maintain space
            }
            ?>
        </div>
        <h3>Items in your cart</h3>
        <form id="cartForm" action="deleteCartProducts.php" method="POST">
            <label for="selectAll">Delete all products</label>
            <input class="checkbox" type="checkbox" id="selectAll">
            <button type="button" id="deleteButton" onclick="showConfirmation()" disabled>Delete</button>
            <div class="box">
                <?php if (isset($productData)):
                    foreach ($productData as $index => $product):
                        $imagePath = "../images/" . $product["image"];
                        $imageSrc = (!empty($product['image']) && file_exists($imagePath)) ? $imagePath : "../images/defaultProduct.png";
                        ?>
                        <div class="product">
                            <div class="productDetail">
                                <div class="checkBoxClass">
                                    <img src="<?= $imageSrc ?>" alt="<?= $product["name"] ?>">
                                </div>
                                <div class="description">
                                    <p class="ellipsis">
                                        <?= htmlspecialchars($product['name']); ?>
                                    </p>
                                    <div class="product-info">
                                        <p>Rs.
                                            <?= number_format($product['price'], 2); ?>
                                        </p>
                                        <p>
                                            <span class="quantity">Cart quantity:
                                                <?= $product['cart_quantity']; ?>
                                            </span>
                                        </p>
                                        <p class="total-amount">
                                            <span>Total amount:</span>
                                            <?= $product['total_amount']; ?>
                                        </p>
                                    </div>
                                    <label class="deleteCheckboxBtn" for="">Select to delete
                                        <input class="checkbox" type="checkbox" name="cart_ids[]"
                                            value="<?= $product['cart_id']; ?>" onchange="toggleDeleteButton()">
                                    </label>
                                </div>
                                <div class="actions-btns">
                                    <a class="order-now-btn" style="background-color:blue;"
                                        href="../product/product_details.php?product_id=<?= $product['product_id']; ?>">View
                                        Product</a>
                                    <a class="order-now-btn"
                                        href="../order/checkout.php?cart_id=<?= $product['cart_id']; ?>">Buy Now</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>
                        <?= htmlspecialchars($error_message); ?>
                    </p>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="confirmation-dialog" id="confirmationDialog">
        <div class="confirmation-dialog-content">
            <p>Are you sure you want to delete the selected items?</p>
            <div class="confirmation-buttons">
                <button class="confirm" onclick="submitForm()">Confirm</button>
                <button class="cancel" onclick="hideConfirmation()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function () {
            var checkboxes = document.querySelectorAll('.checkbox');
            var deleteButton = document.getElementById('deleteButton');
            deleteButton.classList.remove('checked');

            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;

            }
            toggleDeleteButton();
        });

        function toggleDeleteButton() {
            var checkboxes = document.querySelectorAll('.checkbox');
            var deleteButton = document.getElementById('deleteButton');
            var isChecked = false;
            deleteButton.classList.remove('checked');

            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    isChecked = true;
                    deleteButton.classList.add('checked');
                    break;
                }
            }
            deleteButton.disabled = !isChecked;

        }

        function showConfirmation() {
            document.getElementById('confirmationDialog').style.display = 'flex';
        }

        function hideConfirmation() {
            document.getElementById('confirmationDialog').style.display = 'none';
        }

        function submitForm() {
            document.getElementById('cartForm').submit();
        }
    </script>
</body>

</html>