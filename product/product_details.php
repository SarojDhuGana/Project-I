<?php
require_once('ProductManager.php');
require_once('../cart/CartManager.php');
require_once('../comment/CommentManager.php');

session_start();
// to display product details
if (isset($_GET['product_id'])) {
    $response = array();
    $productId = $_GET['product_id'];
    $productManager = new ProductManager();
    $details = $productManager->getProductDetails($productId);
    $productDetails = json_decode($details, true);

} else {
    echo "<p class='error-message'>Product not found</p>";
    exit();
    // header('Location:');
}

// for adding items to cart
if (!empty($_POST)) {
    $buyer_id = $_POST['buyer_id'];
    $product_id = $productId;
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $cartInstance = new CartManager();
    $response = $cartInstance->addCart($buyer_id, $product_id, $quantity, $price);
    $responseDecode = json_decode($response, true);
}
$commentInstance = new CommentManager();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="product_details.css">
    <link rel="stylesheet" href="../style/customDelete.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="../quantityIncrement.js"></script>
    <script>
        // function toggleSection(sectionId) {
        //     var sections = ['reviews-section', 'questions-section', 'comments-section'];
        //     sections.forEach(function (id) {
        //         var section = document.getElementById(id);
        //         if (sectionId === id) {
        //             section.style.display = (section.style.display === "none" || section.style.display === "") ?
        //                 "block" : "none";
        //         } else {
        //             section.style.display = "none";
        //         }
        //     });
        // }

    // function increaseQuantity(inputId, stockValue) {
    //     var input = document.getElementById(inputId);
    //     var currentValue = parseInt(input.value);
    //     if (currentValue < stockValue) {
    //         input.value = currentValue + 1;
    //     }
    // }

    // function decreaseQuantity(inputId) {
    //     var input = document.getElementById(inputId);
    //     var value = parseInt(input.value);
    //     if (value > 1) {
    //         input.value = value - 1;
    //     }
    // }
    </script>
</head>

<body>

    <div class="container">

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

        <div class="container-container">
            <?php if (!empty($responseDecode) && $responseDecode['success'] == false) { ?>
                <p class="error" style="color:red;">
                    <?= $responseDecode['message']; ?>
                </p>
            <?php } else if (!empty($responseDecode) && $responseDecode['success'] == true) { ?>
                    <p class="success" style="color:green;">
                    <?= $responseDecode['message']; ?>
                    </p>
            <?php } ?>
        </div>

        <?php if (!empty($productDetails) && $productDetails['success'] == true) {
            foreach ($productDetails['data'] as $index => $product) {
                $_SESSION['product_id'] = $product['product_id'];
                $imagePath = "../images/" . $product["image"];
                $imageSrc = (!empty($product['image']) && file_exists($imagePath)) ? $imagePath : "../images/defaultProduct.png";
                ?>
                <div class="productfeature">
                    <div class="product-img">
                        <img src="<?= $imageSrc ?>" alt="<?= $product['name']; ?>">
                    </div>
                    <form action="" method="post" id="myForm">
                        <input type="hidden" name="product_id" value="<?= $product['product_id']; ?>">
                        <input type="hidden" name="name" value="<?= $product['name']; ?>">
                        <input type="hidden" name="price" value="<?= $product['price']; ?>">
                        <input type="hidden" name="quantity" id="selected-item-<?= $index ?>-quantity" value="1">
                        <input type="hidden" name="buyer_id"
                            value="<?= isset($_SESSION['buyer_id']) ? $_SESSION['buyer_id'] : ''; ?>">

                        <div class="wrapDescription">
                            <div class="wrapper">
                                <div class="wrapQuantity">
                                    <h1 class="ellipsis">
                                        <?= $product['name'] ?>
                                    </h1>
                                    <p>Price:
                                        <?= $product['price']; ?>
                                    </p>
                                    <p>In Stock:
                                        <?= $product['quantity'] ?>
                                    </p>
                                    <p class="description">Specification or description:</p>
                                    <p>
                                        <?= nl2br(htmlspecialchars($product['description'])); ?>
                                    </p>

                                    <div class="incrementDecrement" style="display:flex;">
                                        <label for="item-<?= $index ?>-quantity">Quantity:</label>
                                        <div class="quantity-input">
                                            <button type="button" class="incrementButton"
                                                onclick="decreaseQuantity('item-<?= $index ?>-quantity')">-</button>
                                            <input type="text" class="cartValue" name="products[<?= $index ?>][quantity]"
                                                id="item-<?= $index ?>-quantity" value="1" readonly>
                                            <button type="button" class="decrementButton"
                                                onclick="increaseQuantity('item-<?= $index ?>-quantity', <?= $product['quantity']; ?>)">+</button>
                                        </div>
                                    </div>


                                    <!-- <div class="checkout-section">
                                <button class="add-to-cart-btn" type="submit">Add to Cart</button>
                                <a href="../cart/showCarts.php" class="view-cart-btn">View Cart</a>
                                <input type="hidden" name="cart_id"
                                    value="<?= isset($product['cart_id']) ? $product['cart_id'] : ''; ?>">
                                <input type="hidden" name="product_id" value="<?= $product['product_id']; ?>">
                                <input type="hidden" name="name" value="<?= $product['name']; ?>">
                                <input type="hidden" name="price" value="<?= $product['price']; ?>">
                                <a class="order-now-btn"
                                    href="../order/checkout.php?product_id=<?= $product['product_id'] ?>">Buy Now</a>
                            </div> -->
                                    <div class="checkout-section">
                                        <button class="addToCartBtn" type="submit">Add to Cart</button>
                                        <a href="../cart/showCarts.php" class="viewCartBtn">View Cart</a>
                                        <input type="hidden" name="cart_id"
                                            value="<?= isset($product['cart_id']) ? $product['cart_id'] : ''; ?>">
                                        <input type="hidden" name="product_id" value="<?= $product['product_id']; ?>">
                                        <input type="hidden" name="name" value="<?= $product['name']; ?>">
                                        <input type="hidden" name="price" value="<?= $product['price']; ?>">
                                        <a class="orderNowBtn"
                                            href="../order/checkout.php?product_id=<?= $product['product_id'] ?>">Buy Now</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <!-- Comment Section -->
                <div id="comments-section" class="comment-section" style="display:block;">
                    <h3>Comments</h3>
                    <?php
                    $product_id = $product['product_id'];
                    $commentInstance = new CommentManager();
                    $commentList = $commentInstance->commentList($product_id);
                    $commentListDecode = json_decode($commentList, true);
                    // Proceed with array_reverse() and display
                    $commentListDecode = array_reverse($commentListDecode);
                    ?>
                    <div class="comment-list">
                        <?php
                        if (!empty($commentListDecode) && isset($commentListDecode['data'])) {
                            $totalComments = count($commentListDecode['data']);
                            $initialDisplayCount = 3; // Number of comments to display initially
                            foreach ($commentListDecode['data'] as $index => $comment): ?>
                                <div class="comment-item" style="<?= $index >= $initialDisplayCount ? 'display: none;' : ''; ?>"
                                    id="comment-<?= $index; ?>">
                                    <p class="commenterName">
                                        <?= htmlspecialchars(isset($comment['buyer_name']) ? $comment['buyer_name'] : $comment['seller_name']); ?>
                                    </p>
                                    <p class="comment-text" id="comment-text-<?= $comment['comment_id']; ?>">
                                        <?= htmlspecialchars($comment['comment_text']); ?>
                                    </p>

                                    <?php if (isset($comment['buyer_id']) && !empty($comment['buyer_id']) == isset($_SESSION['buyer_id'])): ?>
                                        <div class="comment-actions">
                                            <a href="javascript:void(0);" class="edit-comment"
                                                onclick="showEditField(<?= $comment['comment_id']; ?>)">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="../comment/deleteComment.php?comment_id=<?= $comment['comment_id']; ?>"
                                                onclick="showConfirmation(<?= $comment['comment_id'] ?>); return false;"
                                                class="delete-comment">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <div id="edit-field-<?= $comment['comment_id']; ?>" class="edit-field" style="display: none;">
                                        <form action="../comment/editComment.php" method="POST">
                                            <textarea
                                                name="edited_comment"><?= htmlspecialchars($comment['comment_text']); ?></textarea>
                                            <input type="hidden" name="comment_id" value="<?= $comment['comment_id']; ?>">
                                            <input type="submit" value="Save">
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach;

                            if ($totalComments > $initialDisplayCount): ?>
                                <button id="see-all-comments" class="see-all-btn" onclick="showAllComments()">See All</button>
                            <?php endif;
                        } else {
                            echo htmlspecialchars($commentListDecode['message']);
                        }
                        ?>
                    </div>

                </div>
                <form action="../Comment/addComment.php" method="POST" class="comment-form">
                    <textarea name="comment" placeholder="Add your comment here" required></textarea>
                    <input type="hidden" name="user_type" value="buyer">
                    <input type="hidden" name="product_id" value="<?= $product_id; ?>">
                    <input type="hidden" name="user_id"
                        value="<?= (!empty($_SESSION['buyer_id']) && isset($_SESSION['buyer_id'])) ? $_SESSION['buyer_id'] : ''; ?>">
                    <input type="submit" value="Submit Comment">
                </form>
            </div>



            <!-- <div id="questions-section" class="comment-section" style="display:none;">
                    <h3>Questions and Answers</h3>
                    <div class="comment">
                        <p>John Doe: What is the return policy?</p>
                    </div>
                    <form action="../Comment/addQuestion.php" method="POST">
                        <textarea name="question" placeholder="Add your question here" required></textarea>
                        <input type="submit" value="Submit Question">
                    </form>
                </div> -->
        <?php }
        } else {
            echo $productDetails['message'];
        } ?>
    </div>

    <!-- HTML structure for the custom prompt box -->
    <div id="customPromptBox" class="promptBox">
        <div class="promptContent">
            <span id="promptMessage"></span>
            <button id="closePromptBtn">Close</button>
        </div>
    </div>
</body>

</html>

<script>
    // JavaScript to hide the message after 3 seconds
    window.addEventListener('DOMContentLoaded', (event) => {
        const messageElement = document.getElementById('responseMessage');
        if (messageElement) {
            setTimeout(() => {
                messageElement.classList.add('hidden');
            }, 2000); // Start fade out after 3 seconds
            setTimeout(() => {
                messageElement.style.display = 'none';
            }, 3000); // Remove from display after the fade-out transition
        }
    });


    function showAllComments() {
        const commentList = document.querySelectorAll('.comment-item');
        commentList.forEach(comment => comment.style.display = 'block');
        document.getElementById('see-all-comments').style.display = 'none'; // Hide the "See All" button after clicking
    }



    function showEditField(commentId) {
        var commentText = document.getElementById('comment-text-' + commentId);
        var editField = document.getElementById('edit-field-' + commentId);

        if (editField.style.display === 'none') {
            editField.style.display = 'block';
            commentText.style.display = 'none';
        } else {
            editField.style.display = 'none';
            commentText.style.display = 'block';
        }
    }
</script>

<!-- Comment confirmation part -->
<div class="confirmation-dialog" id="confirmationDialog">
    <div class="confirmation-dialog-content">
        <p>Are you sure you want to delete?</p>
        <div class="confirmation-buttons">
            <button class="confirm" onclick="deleteCategory()">Confirm</button>
            <button class="cancel" onclick="hideConfirmation()">Cancel</button>
        </div>
    </div>
</div>

</div>
<script>
    function showConfirmation(productId) {
        // Show the confirmation dialog
        document.getElementById('confirmationDialog').style.display = 'flex';
        // Pass categoryId to deleteCategory function when Confirm button is clicked
        document.getElementById('confirmationDialog').setAttribute('data-category-id', productId);
    }

    function hideConfirmation() {
        // Hide the confirmation dialog
        document.getElementById('confirmationDialog').style.display = 'none';
    }

    function deleteCategory() {
        // Get the category ID from the data attribute of the confirmation dialog
        var productId = document.getElementById('confirmationDialog').getAttribute('data-category-id');
        // Redirect to deleteCategory.php with category ID as parameter
        window.location = "../comment/deleteComment.php?comment_id=" + productId;
    }
</script>