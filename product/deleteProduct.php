<?php
require_once('ProductManager.php');


if (!empty($_GET['pid'])) {
    $product_id = $_GET['pid'];
    $productManager = new ProductManager();
    $productResponse = $productManager->deleteProduct($product_id);
    echo $productResponse;

} else {
    header('Location:../seller/productList.php');
}