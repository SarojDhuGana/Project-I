<?php
// $_SESSION['buyer_id'];
session_start();
require_once ('CommentManager.php');

if (!empty($_POST)) {

    print_r($_POST);
}

?>