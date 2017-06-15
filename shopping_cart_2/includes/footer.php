</div>
<footer class="text-center">&copy; Copyright 2017 Ecommerce Site</footer>
<script>
	$(window).scroll(function(){
		var vscroll = $(this).scrollTop();
    		// console.log(vscroll);
    		$('#logotext').css({"transform" : "translate(0px, "+vscroll/2+"px)"});

    		var vscroll = $(this).scrollTop();
    		// console.log(vscroll);
    		$('#headphone').css({"transform" : "translate("+vscroll/5+"px, -"+vscroll+"px)"});

    		var vscroll = $(this).scrollTop();
    		// console.log(vscroll);
    		$('#speaker').css({"transform" : "translate(-"+vscroll/5+"px, -"+vscroll+"px)"});
  	})
	function detailsmodal(id){
		var data = {"id" : id};
		$.ajax({
			url: 'includes/detailsmodal.php',
			method: "post",
			data: data,
			success: function(data){
				$('body').append(data);
				$('#details-modal').modal('toggle');
			},
			error: function(){
				alert("Something went wrong");
			}
		});
	}
	function updateCart(mode, edit_id, edit_color){
		var data = {'mode' : mode, 'edit_id' : edit_id, 'edit_color' : edit_color};
		$.ajax({
			url : '/php/shopping_cart_2/admin/parsers/update_cart.php',
			method : 'post',
			data : data,
			success: function(){
				location.reload();
			},
			error: function(){	alert ('Something went wrong.');}
		});
	}
	function addToCart(){
		$('#modal_errors').html('');
		var color = $('#color').val();
		var quantity = $('#quantity').val();
		var available = $('#available').val();
		var error1 = '';
		var data = $('#add_product_form').serialize();
		if (color == '' || quantity == '' || quantity == 0) {
			error1 += '<p class="text-danger text-center">You must choose a color and quantity</p>';
			$('#modal_errors').html(error1);
			return;
		} else if (quantity > available){
			error1 += '<p class="text-danger text-center">There are only ' + available + ' available</p>';
			$('#modal_errors').html(error1);
			return;
		} else {
			$.ajax({
				url : '/php/shopping_cart_2/admin/parsers/add_cart.php',
				method : 'post',
				data : data,
				success : function(){
					location.reload();
					//console.log(data);
				},
				error : function(){
					alert('Something went wrong');
				}
			});
			//console.log(data);
		}
	}
</script>
</body>
</html>