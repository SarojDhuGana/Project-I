<?php
require_once ('ProductManager.php');
$imageName = ""; // Initialize $imageName variable

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        // No image uploaded, proceed without saving the image
        // Handle other form fields and create product here
    } else {
        // Image uploaded, proceed with saving the image
        $imageName = $_FILES['image']['name'];
        $temp = $_FILES['image']['tmp_name'];
        if (!move_uploaded_file($temp, '../images/' . $imageName)) {
            return json_encode(['success' => false, 'message' => 'Image upload failed']);
        }
        // Continue with processing the rest of the form data and create product
    }

    $productDetails = [
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'price' => $_POST['price'],
        'category_id' => $_POST['category_id'],
        'seller_id' => $_POST['seller_id'],
        'quantity' => $_POST['quantity'],
        'image' => $imageName
    ];
    
    // print_r($productDetails);
    if (empty($productDetails['name']) || empty($productDetails['price'])|| $productDetails['category_id']|| empty($productDetails['quantity'])) {
    echo "<script>
    alert('Please fill in the following required fields: Name, Price, Category, and Quantity .');
    window.location.href = '{$_SERVER['HTTP_REFERER']}';
  </script>";
    }
    
    $productManager = new ProductManager();
    $response = $productManager->createProduct($productDetails);
    $responseDecode = json_decode($response, true);
    if ($responseDecode['success'] == false) {
        return;
    }

} else {
    // Handle invalid request method (e.g., GET request)
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("success" => false, "message" => "Method not allowed."));
}



?>