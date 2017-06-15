<?php 
require_once'system/init.php';
include'includes/head.php';
include'includes/navigation.php';
include'includes/headerpartial.php';
include 'includes/leftbar.php';

if (isset($_GET['cat'])) {
	$cat_id = sanitize($_GET['cat']);
} else {
	$cat_id = '';
}

$sql = "SELECT * FROM products WHERE categories = '$cat_id'";
$product_query = $db->query($sql);
$category = getCategory($cat_id);

?>

<!--Main Content -->
<div class="col-md-8">
	<div class="row">
		<h2 class="text-center"><?=$category['child']. ' '. $category['parent'];?></h2>
		<?php while($product = mysqli_fetch_assoc($product_query)) : ?>
			<div class="col-md-3">
				<h4><?=$product['title']; ?></h4>
				<?php $photos = explode(',', $product['img']) ?>
				<img class="img-responsive img-thumb" src="<?= $photos[0]; ?>" alt="<?=$product['title'] ?>">
				<p class="list-price text-danger">List Price:	<s>$ <?= $product['list_price']; ?> </s></p>
				<p class="price">Our Price: $ <?= $product['price']; ?></p>
				<button type="button" class="btn btn-sm btn-success" onclick="detailsmodal(<?php echo $product['id'];?>)">Details</button>
			</div>
		<?php endwhile; ?>
	</div>
</div>
<?php 
include 'includes/rightbar.php';
include 'includes/footer.php';
?>


