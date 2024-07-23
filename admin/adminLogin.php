<?php
require_once(__DIR__ . '/AdminManager.php');

if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];
$adminManager=new AdminManager();
$result=$adminManager->login($email,$password);

}
?>