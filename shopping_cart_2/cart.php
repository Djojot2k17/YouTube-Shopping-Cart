<?php 
require_once 'system/init.php';
include 'includes/head.php';
include 'includes/navigation.php';
include 'includes/headerpartial.php';

if ($cart_id != '') {
	$cartQ = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
	$result = mysqli_fetch_assoc($cartQ);
	$items = json_decode($result['items'], true);
	//var_dump($items);
	$i = 1;
	$sub_total = 0;
	$item_count = 0;
}
?>
<div class="col-md-12">
	<div class="row">
		<h2 class="text-center totals-heading">My Shopping Cart</h2>
		<hr>
		<?php if($cart_id == '') : ?>
			<div class="bg-danger">
				<p class="text-center text-danger">Your cart is empty</p>
			</div>
		<?php else : ?>
			<table class="table table-bordered table-condensed table-striped myTable text-center">
				<thead>
					<th class="text-center">#</th>
					<th class="text-center">Item</th>
					<th class="text-center">Price</th>
					<th class="text-center">Quantity</th>
					<th class="text-center">Color</th>
					<th class="text-center">Sub Total</th>
				</thead>
				<tbody>
					<?php 
					foreach ($items as $item) {
						$product_id = $item['id'];
						$productQ = $db->query("SELECT * FROM products WHERE id = '$product_id'");
						$product = mysqli_fetch_assoc($productQ);
						$color_array = explode(',',$product['colors_available']);
						foreach ($color_array as $color_string) {
							$color = explode(':', $color_string);
							if ($color[0] == $item['color']) {
								$available = $color[1];
							}
						}
						?>
						<tr>
							<td><?=$i;?></td>
							<td><?=$product['title'];?></td>
							<td><?=money($product['price']);?></td>
							<td>
								<button class="btn btn-xs btn-default" onclick="updateCart('removeOne', '<?=$product['id'];?>', '<?=$item['color'];?>')">
									<span class="glyphicon glyphicon-minus"></span>
								</button>
								<?=$item['quantity'];?>
								<?php if($item['quantity'] < $available) : ?>
									<button class="btn btn-xs btn-default" onclick="updateCart('addOne', '<?=$product['id'];?>', '<?=$item['color'];?>')">
										<span class="glyphicon glyphicon-plus"></span>
									</button>
								<?php else: ?>
									<span class="text-danger">Max Available</span>
								<?php endif; ?>
							</td>
							<td><?=$item['color'];?></td>
							<td><?=money($item['quantity'] * $product['price']);?></td>
						</tr>
						<?php
						$i++;
						$item_count += $item['quantity'];
						$sub_total += ($product['price'] * $item['quantity']);
					}
					$tax = TAXRATE * $sub_total;
					$tax = number_format($tax, 2);
					$grand_total = $tax+$sub_total;
					?>
				</tbody>
			</table>
			<hr>
			<table class="table table-bordered table-condensed text-center myTable">
				<h2 class="text-center totals-heading">Totals</h2>
				<hr>
				<thead>
					<th class="text-center">Total Items</th>
					<th class="text-center">Sub Total</th>
					<th class="text-center">Tax</th>
					<th class="text-center">Grand Total</th>
				</thead>
				<tbody>
					<tr>
						<td><?=$item_count;?></td>
						<td><?=money($sub_total);?></td>
						<td><?=money($tax);?></td>
						<td><?=money($grand_total);?></td>
					</tr>
				</tbody>
			</table>
			<hr>
			<div class="checkOut">	
				<!-- Button trigger modal -->
				<button type="button" class="btn btn-primary btn-lg pull-right total" data-toggle="modal" data-target="#checkOutModal">
					<span class="glyphicon glyphicon-shopping-cart"></span> Check Out
				</button>
			</div>
			<!-- Modal -->
			<div class="modal fade" id="checkOutModal" tabindex="-1" role="dialog" aria-labelledby="checkOutModalLabel">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<h4 class="modal-title" id="checkOutModalLabel">Shipping Address</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<form action="thankYou.php" method="post" id="payment-form">
									<span class="bg-danger" id="payment-errors"></span>
									<!-- These hidden inputs are for stripe -->
									<input type="hidden" name="tax" value="<?=$tax;?>">
									<input type="hidden" name="sub_total" value="<?=$sub_total;?>">
									<input type="hidden" name="grand_total" value="<?=$grand_total;?>">
									<input type="hidden" name="cart_id" value="<?=$cart_id;?>">
									<input type="hidden" name="description" value="<?=$item_count.' item'.(($item_count > 1)?'s':'').' from My Ecommerce Site';?>">
									<div id="step-one" style="display:block;">
										<div class="form-group col-md-6">
										<label for="full_name">Full Name:</label>
											<input class="form-control" id="full_name" name="full_name" type="text">
										</div>
										<div class="form-group col-md-6">
											<label for="email">Email:</label>
											<input class="form-control" id="email" name="email" type="email">
										</div>
										<div class="form-group col-md-6">
											<label for="street">Street Address:</label>
											<input class="form-control" id="street" name="street" type="text" data-stripe="address_line1">
										</div>
										<div class="form-group col-md-6">
											<label for="street2">Street Address 2:</label>
											<input class="form-control" id="street2" name="street2" type="text" data-stripe="address_line2">
										</div>
										<div class="form-group col-md-6">
											<label for="city">City:</label>
											<input class="form-control" id="city" name="city" type="text" data-stripe="address_city">
										</div>
										<div class="form-group col-md-6">
											<label for="state">State:</label>
											<input class="form-control" id="state" name="state" type="text" data-stripe="address_state">
										</div>
										<div class="form-group col-md-6">
											<label for="zip_code">Zip Code:</label>
											<input class="form-control" id="zip_code" name="zip_code" type="text" data-stripe="address_zip">
										</div>
										<div class="form-group col-md-6">
											<label for="country">Country:</label>
											<input class="form-control" id="country" name="country" type="text" data-stripe="address_country">
										</div>
									</div>
									<div id="step-two" style="display:none;">
										<div class="form-group col-md-3">
											<label for="name">Name on Card:</label>
											<input type="text" id="name" class="form-control" data-stripe="name">
										</div>
										<div class="form-group col-md-3">
											<label for="number">Card Number:</label>
											<input type="text" id="number" class="form-control" data-stripe="number">
										</div>
										<div class="form-group col-md-2">
											<label for="cvc">CVC:</label>
											<input type="text" id="cvc" class="form-control" data-stripe="cvc">
										</div>
										<div class="form-group col-md-2">
											<label for="expire">Expire Month:</label>
											<select id="exp-month" class="form-control" data-stripe="exp_month">
												<option value=""></option>
												<?php for($i = 1; $i < 13; $i++) : ?>
													<option value="<?=$i;?>"><?=$i;?></option>
												<?php endfor; ?>
											</select>
										</div>
										<div class="form-group col-md-2">
											<label for="exp-year">Expire Year:</label>
											<select id="exp-year" class="form-control" data-stripe="exp_year">
												<option value=""></option>
												<?php $yr = date('Y'); ?>
												<?php	for($i=0; $i < 11; $i++) : ?>
													<option value="<?=$yr+$i;?>"><?=$yr+$i;?></option>
												<?php endfor; ?>
											</select>
										</div>
									</div>
								
							</div>	
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>	        					
							<button type="button" class="btn btn-primary" onclick="check_address();" id="next_button">Next>></button>
							<button type="button" class="btn btn-primary" onclick="back_address();" id="back_button" style="display: none;"><< Back</button>
							<button type="submit" class="btn btn-primary" id="checkout_button" style="display:none;">Check Out</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
<script>
	function back_address(){
		$('#payment-errors').html('');
		$('#step-one').css('display', 'block');
		$('#step-two').css('display', 'none');
		$('#next_button').css('display', 'inline-block');
		$('#back_button').css('display', 'none');
		$('#checkout_button').css('display', 'none');
		$('#checkOutModalLabel').html('Shipping Address');
	}
	function check_address(){
		var data = {
			'full_name' : $('#full_name').val(),
			'email' : $('#email').val(),
			'street' : $('#street').val(),
			'street2' : $('#street2').val(),
			'city' : $('#city').val(),
			'state' : $('#state').val(),
			'zip_code' : $('#zip_code').val(),
			'country' : $('#country').val(),
		};
		$.ajax({
			url : '/php/shopping_cart_2/admin/parsers/check_address.php',
			method : 'post',
			data : data,
			success : function(data){
				if (data != 'passed') {
					$('#payment-errors').html(data);
					console.log(data);
				}
				if (data == 'passed') {
					$('#payment-errors').html('');
					$('#step-one').css('display', 'none');
					$('#step-two').css('display', 'block');
					$('#next_button').css('display', 'none');
					$('#back_button').css('display', 'inline-block');
					$('#checkout_button').css('display', 'inline-block');
					$('#checkOutModalLabel').html('Enter your card details');
				}
			},
			error : function(){alert('Something went wrong.');},
		});
	}
	//Using stripe to process card data
	Stripe.setPublishableKey('<?=STRIPE_PUBLIC?>');
	function stripeResponseHandler(status, response){
		var $form = $('#payment-form');

		if(response.error){
			$form.find('#payment-errors').text(response.error.message);
			$form.find('button').prop('disabled', false);
		} else {
			var token = response.id;
			$form.append($('<input type="hidden" name="stripeToken" />').val(token));
			$form.get(0).submit();
		}
	};

	jQuery(function($){
		$('#payment-form').submit(function(event){
			var $form = $(this);

			$form.find('button').prop('disabled', true);

			Stripe.card.createToken($form, stripeResponseHandler);
			return false;
		});
	});

</script>
<?php	include 'includes/footer.php';