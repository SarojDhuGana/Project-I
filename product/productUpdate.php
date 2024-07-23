<?php
require_once ('ProductManager.php');

$imageName = ""; // Initialize $imageName variable

if (!empty($_POST)) {
    if ($_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        // No new image uploaded, use the current image
        $imageName = $_POST['current_image'];
    } else {
        // New image uploaded, proceed with saving the image
        $imageName = $_FILES['image']['name'];
        $temp = $_FILES['image']['tmp_name'];
        if (!move_uploaded_file($temp, '../images/' . $imageName)) {
            echo json_encode(['success' => false, 'message' => 'Image update failed']);
            exit; // Terminate execution if image upload fails
        }
    }

    $productDetails = [
        'name' => $_POST['name'],
        'product_id' => $_POST['product_id'],
        'description' => $_POST['description'],
        'price' => $_POST['price'],
        'category_id' => $_POST['category_id'],
        'quantity' => $_POST['quantity'],
        'image' => $imageName // $imageName will either be the existing image or the new one
    ];

    $productManager = new ProductManager();
    $response = $productManager->updateProduct($productDetails);

    // Redirect or show success message
    if (!empty($response) && isset($response['success'])) {
        header('Location: productList.php');
        exit();
    } else {
        // echo $response['message'];
        echo "Error updating product";
        exit;
    }
}
?>