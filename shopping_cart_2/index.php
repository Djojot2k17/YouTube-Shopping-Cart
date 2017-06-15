<?php 
require_once'system/init.php';
include'includes/head.php';
include'includes/navigation.php';
include'includes/headerfull.php';
include 'includes/leftbar.php';
$sql = "SELECT * FROM products WHERE featured = 1";
$featured = $db->query($sql);
 
?>
<!--Main Content -->
<div class="col-md-8">
	<div class="row">
		<h2 class="text-center">Featured Products</h2>
		<?php while($product = mysqli_fetch_assoc($featured)) : ?>
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