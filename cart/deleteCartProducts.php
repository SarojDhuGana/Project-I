<?php
require_once('CartManager.php');
session_start();
if (!empty($_SESSION['buyer_id'])) {
    if (isset($_POST["cart_ids"]) && !empty($_POST["cart_ids"])) {
        $cart_ids = $_POST["cart_ids"]; // Get the array of cart IDs from the form

        $cartManager = new CartManager();
        $response = $cartManager->removeProductsFromCart($cart_ids); // Call the removeProductsFromCart method
        $responseDecode = json_decode($result, true); // Decode the JSON string

        if (!empty($responseDecode) && isset($responseDecode['success'])) {
            $_SESSION['success_message'] = $responseDecode['message'];
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit;
        } else {
            $_SESSION['error_message'] = $responseDecode['message'];
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit;
        }
    } else {
        $_SESSION['error_message'] = "Product is not selected";
        header("Location: {$_SERVER['HTTP_REFERER']}");

    }
} else {
    header('Location:../allUserLogin.php');
}
?>
<style>
    .success {
        color: green;
    }

    .error {
        color: red;
    }
</style>