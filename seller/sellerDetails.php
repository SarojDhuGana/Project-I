<?php

require_once ('SellerManager.php');
session_start();
$sellerManager = new SellerManager();

if (!empty($_SESSION['seller_id'])) {
    if ($_GET['id']) {
        $id = $_GET['id'];
        $response = $sellerManager->sellerDetails($id);
        echo $response;
    } else {
        echo "Id not found";
    }
} else {
    header('Location:../allUserLogin.php');
}


?>