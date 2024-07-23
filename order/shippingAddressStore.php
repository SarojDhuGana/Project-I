<?php
require_once ('Order.php');
$orderInstance = new Order();

// echo "pugyo";

if (!empty($_POST['cart_id'] && !empty($_POST['buyer_id'] && !empty($_POST['seller_id']) && !empty($_POST['full_name']) && !empty($_POST['phone_number'])))) {
    print_r($_POST);
    $shippingDetails = [
        'buyer_id' => $_POST['buyer_id'],
        'seller_id' => $_POST['seller_id'],
        'cart_id' => $_POST['cart_id'],
        'full_name' => $_POST['full_name'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'district' => $_POST['district'],
        'city' => $_POST['city'],
        'street' => $_POST['street'],
        'state' => $_POST['state'],
        'zip' => $_POST['zip'],

    ];
    $shippingResponse = $orderInstance->storeShippingAddress($shippingDetails);
} else {
    echo "Fields are required in shippingAddressStore.php file ";
}
?>