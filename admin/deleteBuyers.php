<?php
require_once (__DIR__ . '/AdminManager.php');


$adminManager = new AdminManager();
if (isset($_POST["user_ids"]) && !empty($_POST["user_ids"])) {
    $user_ids = $_POST["user_ids"]; // Get the array of user IDs from the form

    $result = $adminManager->deleteMultipleBuyers($user_ids); // Call the deleteMultipleUsers method
    $resultArray = json_decode($result, true);

    if (is_array($resultArray)) {
        session_start();
        // if ($resultArray['success'] == true) {

        if ($resultArray['success'] == true) {
            $_SESSION['success_message'] = $resultArray['message'];
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        } else {
            $_SESSION['error_message'] = $resultArray['message'];
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        }
    } else {
        echo '<p class="error">Invalid response format.</p>';
    }
} elseif (isset($_GET['user_id'])) {
    // Deleting a single user via link
    $user_id = $_GET['user_id'];
    $result = $adminManager->deleteUserById($user_id);
    $resultArray = json_decode($result, true);

    if (is_array($resultArray)) {
        session_start();
        if ($resultArray['success'] == true) {
            $_SESSION['success_message'] = $resultArray['message'];
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        } else {
            $_SESSION['error_message'] = $resultArray['message'];
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        }
    } else {
        echo '<p class="error">Invalid response format.</p>';
    }
} else {
    echo '<script type="text/javascript">';
    echo 'alert("Item is not selected");';
    echo 'window.location.href = "buyersList.php";';
    echo '</script>';
    exit; // Make sure to stop the script execution after the header
}


?>
<style>
    .success {
        color: green;
    }

    .error {
        color: ;
    }
</style>