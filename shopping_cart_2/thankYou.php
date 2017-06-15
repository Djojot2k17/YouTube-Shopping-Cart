<?php 
//using stripe to process the credit card
require_once 'system/init.php';
\Stripe\Stripe::setApiKey(STRIPE_PRIVATE);
$token=$_POST['stripeToken'];
//get the rest of the post data
$full_name = sanitize($_POST['full_name']);
$email = sanitize($_POST['email']);
$street = sanitize($_POST['street']);
$street2 = sanitize($_POST['street2']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$zip_code = sanitize($_POST['zip_code']);
$country = sanitize($_POST['country']);
$tax = sanitize($_POST['tax']);
$sub_total = sanitize($_POST['sub_total']);
$grand_total = sanitize($_POST['grand_total']);
$cart_id = sanitize($_POST['cart_id']);
$description = sanitize($_POST['description']);
$charge_amount = number_format($grand_total,2) * 100;
$metadata = array(
	'cart_id' => $cart_id,
	'tax' => $tax,
	'sub_total' => $sub_total
	);

try{
	$charge = \Stripe\Charge::create(array(
	'amount' => $charge_amount, // amount in cents
	'currency' => CURRENCY,
	'source' => $token,
	'description' => $description,
	'receipt_email' => $email,
	'metadata' => $metadata)
);
//adujst inventory
$itemQ = $db->query("SELECT * FROM cart WHERE id = '$cart_id'");
$iResults = mysqli_fetch_assoc($itemQ);
$items = json_decode($iResults['items'],true);
foreach ($items as $item) {
	$newColors = array();
	$item_id = $item['id'];
	$productQ = $db->query("SELECT colors_available FROM products WHERE id = '$item_id'");
	$product = mysqli_fetch_assoc($productQ);
	$colors = colorsToArray($product['colors_available']);
	foreach ($colors as $color) {
		if($color['color'] == $item['color']){
			$q = $color['quantity'] - $item['quantity'];
			$newColors[] = array('color' => $color['color'], 'quantity' => $q);
		} else {
			$newColors[] = array('color' => $color['color'], 'quantity' => $color['quantity']);
		}
	}
	$colorString = colorsToString($newColors);
	$db->query("UPDATE products SET colors_available = '$colorString' WHERE id = '$item_id'");
}
//update cart
$db->query("UPDATE cart SET paid = 1 WHERE id = '$cart_id'");
$db->query("INSERT INTO transactions (charge_id, cart_id, full_name, email, street, street2, city, state, zip_code, country, sub_total, tax, grand_total, description, txn_type) 
							VALUES ('$charge->id', '$cart_id', '$full_name','$email', '$street', '$street2', '$city', '$state', '$zip_code', '$country', '$sub_total', '$tax', '$grand_total', '$description', '$charge->object')");
$domain = ($_SERVER['HTTP_HOST'] != 'localhost')?'.'.$_SERVER['HTTP_HOST']:false;
setcookie(CART_COOKIE, '',1,'/',$domain,false);
include 'includes/head.php';
include 'includes/navigation.php';
include 'includes/headerpartial.php';
?>
	<h1 class="text-center text-success">Thank You!</h1>
	<p>Your card has been successfully charged <?=money($grand_total);?>. You have been emailed a receipt. Please check your spam folder if it is not in your inbox. Additionally you can print this page as a receipt.</p>
	<p>Your receipt number is: <strong><?=$cart_id;?></strong></p>
	<p>Your order will be shipped to the address below.</p>
	<address>
		<?=$full_name;?><br>
		<?=$street;?><br>
		<?=(($street2 != '')?$street2.'<br>':'');?>
		<?=$city. ', '. $state. ' '. $zip_code;?><br>
		<?=$country?><br>
	</address>
<?php
include 'includes/footer.php';
} catch(\Stripe\Error\Card $e){
	//The card has been declined
	echo $e;
}
function colorsToArray($cstring){
	$colorsArray = explode(',',$cstring);
	$returnArray = array();
	foreach ($colorsArray as $color) {
		$c = explode(':',$color);
		$returnArray[] = array('color' => $c[0], 'quantity' =>$c[1]);
	}
	return $returnArray;
}
function colorsToString($colors){
	$colorString = '';
	foreach ($colors as $color) {
		$colorString .= $color['color'].':'.$color['quantity'].',';
	}
	$trimmed = rtrim($colorString, ',');
	return $trimmed;
}
?>