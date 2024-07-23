<?php
require_once('SellerManager.php');
session_start();
if (!empty($_SESSION['seller_id'])) {
    $seller_id = $_SESSION['seller_id'];
    if (isset($seller_id)) {
        $sellerManager = new SellerManager();
        $product = $sellerManager->viewAllProducts($seller_id);
        $productDecode = json_decode($product, true); // Decode JSON string into an associative array
    } else {
        echo "Invalid seller ID.";
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
    <title>Product List</title>
    <link rel="stylesheet" href="productList.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<style>
    /* Custom confirmation dialog styles */
    .confirmation-dialog {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .confirmation-dialog-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    .confirmation-buttons {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .confirmation-buttons button {
        margin: 0 10px;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    .confirmation-buttons button.confirm {
        background-color: #dc3545;
        color: white;
    }

    .confirmation-buttons button.cancel {
        background-color: #007bff;
        color: white;
    }
</style>

<body>
    <div class="container">
        <h2>Product List</h2>
        <table>
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th class="action">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($productDecode) && $productDecode['success'] == true) {
                    foreach ($productDecode['data'] as $key => $product) {
                        $imagePath = "../images/" . $product["image"];
                        $imageSrc = (!empty($product['image']) && file_exists($imagePath)) ? $imagePath : "../images/defaultProduct.png";
                        ?>
                        <tr>
                            <td>
                                <?= ++$key; ?>
                            </td>
                            <td>
                                <a href="sellerProduct_details.php?product_id=<?= $product['product_id']; ?>">
                                    <img class="img" src="<?= $imageSrc ?>" alt="<?= $product["name"] ?>">
                                </a>
                            </td>
                            <td class="ellipsis">
                                <a href="sellerProduct_details.php?product_id=<?= $product['product_id']; ?>"
                                    style="text-decoration:none; color: var(--text-color);">
                                    <?= $product['name'] ?>
                                </a>
                            </td>
                            <td>
                                <?= $product['price'] ?>
                            </td>
                            <td>
                                <?= $product['quantity'] ?>
                            </td>
                            <td class="actionBtns">
                                <a href="../product/productEdit.php?pid=<?= $product['product_id'] ?>" class="edit"><i
                                        class="fa fa-edit"></i> Edit</a>
                                <a href="#" onclick="showConfirmation(<?= $product['product_id'] ?>); return false;"
                                    class="delete"><i class="fa fa-trash-o"></i> Delete</a>
                            </td>
                        </tr>
                    <?php }
                } else {
                    echo '<tr><td colspan="6">' . $productDecode['message'] . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

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
            window.location = "../product/deleteProduct.php?pid=" + productId;
        }
    </script>
</body>

</html>