<?php
require_once ('CommentManager.php');
session_start();
if (!empty($_POST['edited_comment'])) {
    $edited_comment = $_POST['edited_comment'];
    $comment_id = $_POST['comment_id'];

    // print_r($_POST);
    $commentInstance = new CommentManager();
    $response = $commentInstance->updateComment($comment_id, $edited_comment);
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
    echo "Comment is required";
}


?>