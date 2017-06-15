<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/php/shopping_cart_2/system/init.php';
$mode = sanitize($_POST['mode']);
$edit_id = sanitize($_POST['edit_id']);
$edit_color = sanitize($_POST['edit_color']);
$cart_query = $db->query("SELECT * FROM cart WHERE id = '$cart_id'");
$result = mysqli_fetch_assoc($cart_query);
$items = json_decode($result['items'], true);
$updated_items = array();
$domain = ($_SERVER['HTTP_HOST'] != 'localhost')?'.'.$_SERVER['HTTP_HOST']:false;
if ($mode == 'removeOne') {
	foreach ($items as $item) {
		if ($item['id'] == $edit_id && $item['color'] == $edit_color) {
			$item['quantity'] = $item['quantity'] - 1;
		}
		if ($item['quantity'] > 0) {
			$updated_items[] = $item;
		}
	}
}
if ($mode == 'addOne') {
	foreach ($items as $item) {
		if ($item['id'] == $edit_id && $item['color'] == $edit_color) {
			$item['quantity'] = $item['quantity'] + 1;
		}
		$updated_items[] = $item;
	}
}

if(!empty($updated_items)){
	$json_update = json_encode($updated_items);
	$db->query("UPDATE cart SET items = '$json_update' WHERE id = $cart_id");
		//$_SESSION['success_flash'] = 'Your shopping cart has been updated';
}

if (empty($updated_items)) {
	$db->query("DELETE FROM cart WHERE id = $cart_id");
	setcookie(CART_COOKIE,'',1,'/',$domain,false);
}