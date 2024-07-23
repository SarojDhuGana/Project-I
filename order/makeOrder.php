<?php
require_once ('Order.php');
$order = new Order();
session_start();
if (!empty($_SESSION['buyer_id'])) {
    $buyer_id = $_POST['buyer_id'];
    $seller_id = $_POST['seller_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $total_amount = isset($_POST['total_amount']) ? $_POST['total_amount'] : '';

    // echo "<br>total amuont" . $total_amount;

    // $cart_id = $_POST['cart_id'];
    //  if following details are not filled then return 
    if (empty($_POST['phone_number']) || empty($_POST['full_name']) || empty($_POST['district']) || empty($_POST['city'])) {
        echo "<script>
                alert('Please fill in the following required fields: Phone Number, Full Name, District, and City .');
                window.location.href = '{$_SERVER['HTTP_REFERER']}';
              </script>";
        exit();
    }


    // print_r();
    $shippingDetails = [
        'buyer_id' => $_POST['buyer_id'],
        'seller_id' => $_POST['seller_id'],
        'cart_id' => isset($_POST['cart_id']) ? $_POST['cart_id'] : '',
        'full_name' => $_POST['full_name'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'district' => $_POST['district'],
        'city' => $_POST['city'],
        'street' => isset($_POST['street']) ? $_POST['street'] : '',
        'state' => isset($_POST['state']) ? $_POST['state'] : '',
        'postal_code' => isset($_POST['zip']) ? $_POST['zip'] : '',
    ];

    $orderResponse = $order->makeOrder($buyer_id, $seller_id, $product_id, $quantity, $total_amount, $shippingDetails);
    $orderResponseDecode = json_decode($orderResponse, true);
    // echo "<br>";
    // print_r($orderResponseDecode);

} else {
    header('Location:../allUserLogin.php');
}
?>