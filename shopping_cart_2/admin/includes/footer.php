</div>  
<footer class="text-center">&copy; Copyright 2017 Ecommerce Site</footer>
 
<script>
	function updateColors(){
		var colorString = '';
		for (var i = 1; i <= 6; i++) {
			if ($('#color'+i).val()!= '') {
				colorString += $('#color'+i).val()+ ':' + $('#qty'+i).val() + ',';
			}
		}
		$('#colors').val(colorString);
	}
	function get_child_options(selected){
		if (typeof selected === 'undefined') {
			var selected = '';
		}
		var parentID = $('#parent').val();
		
		$.ajax({
			url: '/php/shopping_cart_2/admin/parsers/child_categories.php',
			type: 'POST',
			data: {parentID : parentID, selected: selected},
			success: function(data){
				$('#child').html(data);
			}
		});
	}
	$('select[name="parent"]').change(function(){
		get_child_options();
	});
</script>

</body>
</html>