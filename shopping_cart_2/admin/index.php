<?php 
require_once '../system/init.php';
if (!isLoggedIn()) {
	header('Location: login.php');
}
include 'includes/head.php';
include 'includes/navigation.php';
?>

Administrator Home

<?php
include 'includes/footer.php'; 
?>