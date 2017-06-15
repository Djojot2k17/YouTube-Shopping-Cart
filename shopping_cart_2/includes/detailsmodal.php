<?php 
require_once '../system/init.php';
$id = $_POST['id'];
$id = (int)$id;
$sql = "SELECT * FROM products WHERE id=$id";
$result = $db->query($sql);
$product = mysqli_fetch_assoc($result);
$brand_id = $product['brand'];
$sql_brand = "SELECT brand FROM brand WHERE id=$brand_id";
$brand_result = $db->query($sql_brand);
$brand = mysqli_fetch_assoc($brand_result);
$colors_available_string = $product['colors_available'];
$colors_array = explode(',', $colors_available_string);

//var_dump($brand); 

?>
<?php ob_start();?>
<div class="modal fade details-1" id="details-modal" role="dialog" aria-labelledby="details-1" aria-hidden="true">
<div class="modal-dialog modal-md">
	<div class="modal-content">
  		<div class="modal-header">
			<button class="close" type="button" onclick="closeModal()" aria-label="close">
				<span aria-hidden="true">&times;</span> 
			</button>
			<h4 class="modal-title text-center"><?= $product['title']; ?></h4>
		</div>
		<!-- End Modal Header -->
		<div class="modal-body">
			<div class="container-fluid">
				<div class="row">
					<span id="modal_errors" class="bg-danger"></span>
						<div class="col-sm-6 fotorama">
						<?php $photos = explode(',',$product['img']);
						foreach($photos as $photo):?>
							<img class= "img-responsive" src="<?= $photo; ?>" alt="<?= $product['title']; ?>">
						<?php endforeach; ?>
						</div>
					<div class="col-sm-6">
						<h4>Details</h4>
						<p><?=nl2br($product['description']); ?></p>
						<hr>
						<p>Price: $ <?= $product['price']; ?></p>
						<p>Brand: <?= $brand['brand']; ?></p>
						<div class="row">
							<form action="add_cart.php" method="POST" id="add_product_form">
								<input type="hidden" name="product_id" id="<?=$id;?>" value="<?=$id;?>">
								<input type="hidden" name="available" id="available" value="">
								<div class="form-group">
									<div class="col-xs-4">
										<label for="quantity">Quantity:</label>
										<input type="number" class="form-control" id="quantity" name="quantity" min="1">
									</div>                
								</div>
								<div class="form-group">
									<div class="col-xs-8">
										<label for="color">Colors: </label>
										<select name="color" id="color" class="form-control">
											<option value=""></option>
											<?php foreach ($colors_array as $string) {
												$string_array = explode(':', $string);
												$color = $string_array[0];
												$available = $string_array[1];
												echo '<option value="'.$color.'" data-available="'.$available.'">'.$color. ' ('.$available.' Available)</option>';
											} ?>
										</select>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>    
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-default" onclick="closeModal()">Close</button>
			<button class="btn btn-warning" onclick="addToCart(); return false;"><span class="glyphicon glyphicon-shopping-cart"></span>Add To Cart</button>
		</div>
	</div>
</div>
</div>
<script>
	$('#color').change(function(){
		var available = $('#color option:selected').data('available');
		$('#available').val(available);
	})
	$(function () {
  		$('.fotorama').fotorama({'loop':true, 'autoplay':true});
	});
	function closeModal(){
		$('#details-modal').modal('hide');
		setTimeout(function(){
			$('#details-modal').remove();
		},500)
	}
</script>
<?php echo ob_get_clean(); ?>