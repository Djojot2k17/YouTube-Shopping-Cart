<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/php/shopping_cart_2/system/init.php';
unset($_SESSION['SBUser']);
header('Location: login.php');
?>