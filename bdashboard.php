<?php
require_once ('BuyerManager.php');
require_once ('product/ProductManager.php');
require_once ('admin/category/CategoryManager.php');
$productManager = new ProductManager();
$buyerManager = new BuyerManager();
$categoryManager = new CategoryManager();
$categoryList = $categoryManager->list();
$categoryDecode = json_decode($categoryList, true);

// for displaying products
$data = $productManager->viewProductList();
$productData = json_decode($data, true);

// for buyer
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
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="bdashboard.css">
    <script src="JsFunctions/searchBoxHandle.js"></script>


</head>

<body>

    <?php require_once ('buyerNavigation.php'); ?>

    <section id='collection'>
        <div class='collection-container'>
            <h2>Products</h2>
        </div>
        <div class='collections container'>
            <?php
            if (!empty($productData) && $productData['success'] == true) {
                foreach ($productData['data'] as $product) { 
                    $imagePath = "images/" . $product["image"];
                    $imageSrc = (!empty($product['image']) && file_exists($imagePath) )? $imagePath : "images/defaultProduct.png";
                   
                    ?>
                    <div class='content'>
                    <img src="<?= $imageSrc ?>" alt="<?= $product["name"] ?>">
                    <div class='img-content'>
                            <p id="productName"> <?= $product['name']; ?></p>
                            <p>NPR. <?= $product['price']; ?></p>
                            <button>
                                <a href="product/product_details.php?product_id=<?= $product['product_id']; ?>">View Product</a>
                            </button>
                        </div>
                    </div>
                <?php }
            } else {
                echo $productData['message'];
            } ?>
        </div>
    </section>

    <script>
        document.getElementById('hamburger').addEventListener('click', function () {
            document.getElementById('mainNav').classList.toggle('show');
        });


        // Other event listeners and functions

        document.getElementById('categorySelect').addEventListener('change', function () {
            var url = this.value;
            if (url) {
                window.location.href = url;
            }
        });
    </script>
</body>

</html>