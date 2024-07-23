<?php

require_once ('CartManager.php');
$cartInstance = new CartManager();
session_start();
if (empty($_SESSION['buyer_id'])) {
    header('Location:../allUserLogin.php');
}
if (!empty($_POST)) {
    // $email = $_POST['email']; // make this hidden
    $buyer_id = $_POST['buyer_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $response = $cartInstance->addCart($buyer_id, $product_id, $quantity, $price);
    $responseDecode = json_decode($response, true);
    if (!empty($responseDecode) && $responseDecode['success'] == true) {
        echo "<script>
    if(confirm('Added to cart. Want to view cart?')==true){
    window.location.href='showCarts.php';
    }else{
       window.location.href = '{$_SERVER['HTTP_REFERER']}';
        }    </script>";
    }
}



?>