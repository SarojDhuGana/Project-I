<?php
require_once ('ProductManager.php');
$productManager = new ProductManager();
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $response = array();
    $data = $productManager->viewProductList();
    $productData = json_decode($data, true);

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Nunito', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #f7f7f7;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .card {
            flex: 0 0 calc(33.33% - 20px);
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .product {
            padding: 20px;
        }

        .product img {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }

        .product h2 {
            margin: 10px 0 5px;
            font-size: 1.2rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product p {
            margin: 0;
            font-size: 1rem;
        }

        @media screen and (max-width: 992px) {
            .card {
                flex-basis: calc(50% - 20px);
            }
        }

        @media screen and (max-width: 576px) {
            .card {
                flex-basis: calc(100% - 20px);
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row">
            <?php foreach ($productData as $product) { ?>
                <a href="product_details.php?id=<?= $product['product_id'] ?>" class="card">
                    <div class="product">
                        <img src="../images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                        <h2><?= $product['name'] ?></h2>
                        <p><?= $product['price'] ?></p>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>

</body>

</html>