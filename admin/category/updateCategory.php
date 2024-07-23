<?php
require_once 'CategoryManager.php';
$imageName = ""; // Initialize $imageName variable

if (!empty($_POST)) {

    if ($_FILES['categoryImage']['error'] === UPLOAD_ERR_NO_FILE) {
        // No image uploaded, proceed without saving the image
        // Handle other form fields and create product here
        $imageName = $_POST['current_image'];

    } else {
        $imageName = $_FILES['categoryImage']['name'];
        $temp = $_FILES['categoryImage']['tmp_name'];
        if (!move_uploaded_file($temp, '../../categoryImage/' . $imageName)) {
            echo json_encode(['success' => false, 'message' => 'Image upload failed']);
            exit();
        }
        // Continue with processing the rest of the form data and create product
    }
      
        $name = $_POST['name'];
        $cat_id = $_POST['cat_id'];
        $categoryManager = new CategoryManager();
        $result = $categoryManager->updateCategory($name, $imageName, $cat_id);
        $resultDecode = json_decode($result, true);
} else {
    // Handle invalid request method (e.g., GET request)
    // http_response_code(405); // Method Not Allowed
    // echo json_encode(array("success" => false, "message" => "Method not allowed."));
    header("Location:../index.php");
}
?>