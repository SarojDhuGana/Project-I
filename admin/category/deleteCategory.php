<?php
require_once 'CategoryManager.php';
session_start();
// Check if cat_id is provided in the URL
if (!empty($_SESSION['admin_id'])) {


    if (!empty($_GET['cat_id'])) {
        // Get the cat_id from the URL
        $cat_id = $_GET['cat_id'];

        // Create an instance of CategoryManager
        $categoryManager = new CategoryManager();

        // Call deleteCategory method to delete the category item
        $result = $categoryManager->deleteCategory($cat_id);

        // Decode the JSON response
        $resultDecode = json_decode($result, true);

        // Check if the response is not empty and successful
        if (!empty($resultDecode) && $resultDecode['success']) {
            // If successful, redirect back to categoryList.php with success message
            $message = urlencode($resultDecode['message']);
            echo "Redirecting with message: $message";
            header("Location: categoryList.php?message=$message");
            exit();
        } else {
            // If unsuccessful, redirect back to categoryList.php with error message
            $error = urlencode($resultDecode['message']);
            echo "Redirecting with error: $error";
            header("Location: categoryList.php?error=$error");
            exit();
        }
    } else {
        // If cat_id is not provided, redirect back to categoryList.php with error message
        $error = urlencode('Category ID is missing');
        echo "Redirecting with error: $error";
        header("Location: categoryList.php?error=$error");
        exit();
    }
} else {
    header('Location:../index.php');
}

?>