<?php
require_once('CommentManager.php');
session_start();
// if (!empty($_POST)) {
//     $commentText = $_POST['comment'];
//     $commenter_id = $_SESSION['buyer_id'];
//     $product_id = $_SESSION['product_id'];

//     print_r($_POST);
//     exit();
//     $commentInstance = new CommentManager();
//     $response = $commentInstance->addComment($product_id, $commenter_id, $commentText);
//     $responseDecode = json_decode($response, true);


//     if (!empty($responseDecode) && isset($responseDecode['success'])) {
//         $_SESSION['success_message'] = $responseDecode['message'];
//         header("Location: {$_SERVER['HTTP_REFERER']}");
//         exit;
//     } else {
//         $_SESSION['error_message'] = $responseDecode['message'];
//         header("Location: {$_SERVER['HTTP_REFERER']}");
//         exit;
//     }
// }

// print_r($_SESSION);


// if ($_SERVER['REQUEST_METHOD'] === 'POST') {

if (!empty($_POST['user_id'])) {
    $product_id = $_POST['product_id'];
    $comment_text = $_POST['comment'];
    $user_type = $_POST['user_type'];
    $user_id = $_POST['user_id'];
    $commentInstance = new CommentManager();
    if ($user_type === 'buyer') {
        $response = $commentInstance->addComment($product_id, $comment_text, $user_id, null);
    } elseif ($user_type === 'seller') {
        $response = $commentInstance->addComment($product_id, $comment_text, null, $user_id);
    } else {
        $response = [
            'success' => false,
            'message' => 'Invalid user type.'
        ];
    }
    $responseDecode = json_decode($response, true);

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
    header('Location:../allUserLogin.php');
    exit();
}
// } else {
//     header('Location:../allUserLogin.php');
//     exit;
// }

?>