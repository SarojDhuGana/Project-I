<?php
// Include your CartManager class
require_once 'CartManager.php';

// Instantiate CartManager
$cartManager = new CartManager();

// Call removeExpiredFromCart() function
$result = $cartManager->removeExpiredFromCart();

// Output result
if ($result['success']) {
    echo "Expired products removed from cart successfully\n";
} else {
    echo "Error: " . $result['message'] . "\n";
}
?>