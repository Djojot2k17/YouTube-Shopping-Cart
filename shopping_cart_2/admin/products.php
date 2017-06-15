<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/php/shopping_cart_2/system/init.php';
if (!isLoggedIn()) {
	loginErrorRedirect();
}
include 'includes/head.php';
include 'includes/navigation.php';
$db_path = '';

//Delete product from site, keep in database
if (isset($_GET['delete'])) {
	$id = sanitize($_GET['delete']);
	$db->query("UPDATE products SET deleted = '1' WHERE id = '$id'");
	header('Location: products.php');
} 

if (isset($_GET['add']) || isset($_GET['edit'])) { 
	$brand_query = $db->query("SELECT * FROM brand ORDER BY brand");
	$parent_query = $db->query("SELECT * FROM categories where parent = 0 ORDER BY category");
	$title = ((isset($_POST['title'])&& $_POST['title'] != '')?sanitize($_POST['title']):'');
	$brand = ((isset($_POST['brand'])&& !empty($_POST['brand']))?sanitize($_POST['brand']):'');
	$parent = ((isset($_POST['parent'])&& !empty($_POST['parent']))?sanitize($_POST['parent']):'');
	$category = ((isset($_POST['child'])) && !empty($_POST['child'])?sanitize($_POST['child']):'');
	$price = ((isset($_POST['price'])&& $_POST['price'] != '')?sanitize($_POST['price']):'');
	$list_price = ((isset($_POST['list_price'])&& $_POST['list_price'] != '')?sanitize($_POST['list_price']):'');
	$description = ((isset($_POST['description'])&& $_POST['description'] != '')?sanitize($_POST['description']):'');
	$colors = ((isset($_POST['colors_available'])&& $_POST['colors_available'] != '')?sanitize($_POST['colors_available']):'');
	$colors = rtrim($colors, ',');
	$saved_image = '';

	if (isset($_GET['edit'])) {
		$edit_id = (int)$_GET['edit'];
		$presults = $db->query("SELECT * FROM products WHERE id= '$edit_id'");
		$product = mysqli_fetch_assoc($presults);
		if (isset($_GET['delete_image'])) {
			$imgi = (int)$_GET['imgi'] -1;
			$images = explode(',',$product['img']);
			$image_url = $_SERVER['DOCUMENT_ROOT'].$images[$imgi];
			unlink($image_url);
			unset($images[$imgi]);
			$imageString = implode(',',$images);
			$db->query("UPDATE products SET img = '$imageString' WHERE id = '$edit_id'");
			header('Location: products.php?edit='.$edit_id);
		}
		$category = ((isset($_POST['child']) && $_POST['child'] != '')? sanitize($_POST['child']):$product['categories']);
		$title = ((isset($_POST['title']) && !empty($_POST['title']))?sanitize($_POST['title']):$product['title']);
		$brand = ((isset($_POST['brand']) && !empty($_POST['brand']))?sanitize($_POST['brand']):$product['brand']);
		$parentQ = $db->query("SELECT * FROM categories WHERE id= '$category'");
		$parentResult = mysqli_fetch_assoc($parentQ);
		$parent = ((isset($_POST['parent']) && !empty($_POST['parent']))?sanitize($_POST['parent']):$parentResult['parent']);
		$price = ((isset($_POST['price']) && !empty($_POST['price']))?sanitize($_POST['price']):$product['price']);
		$list_price = ((isset($_POST['list_price']) && !empty($_POST['list_price']))?sanitize($_POST['list_price']):$product['list_price']);
		$description = ((isset($_POST['description']) && !empty($_POST['description']))?sanitize($_POST['description']):$product['description']);
		//var_dump($product);
		$colors = ((isset($_POST['colors_available']) && !empty($_POST['colors_available']))?sanitize($_POST['colors_available']):$product['colors_available']);
		$colors = rtrim($colors, ',');
		$saved_image = (($product['img'] != '')?$product['img']:'');
		$db_path = $saved_image;
	}
	//format color string to fit database
	if (!empty($colors)) {
		$colors_string = $colors;
		$colors_string = rtrim($colors_string, ',');
		$colors_array = explode(',', $colors_string);
		$c_array = array();
		$q_array = array();
		foreach ($colors_array as $color_string) {
			$cq_string = explode(':', $color_string);
			$c_array[] = $cq_string[0];
			$q_array[] = $cq_string[1];
		}
	} else {
		$colors_array = array();
	}
	if ($_POST) {
		//create errors array
		$errors = array();
		
		//check that all fields are filled
		$required = array('title', 'brand', 'price', 'parent', 'child', 'colors');
		$photoName = array();
		$tmpLoc = array();
		$allowed = array('png', 'jpg', 'jpeg', 'gif');
		$upload_location = array();
		foreach ($required as $field) {
			if (!$_POST[$field] == '') {
				$errors[] = 'All fields with an asterisk are required.';
				break;
			}
		}
		//check uploaded files and upload paths
		$photoCount = count($_FILES['photo']['name']);
		 if ($photoCount > 0) {
		 	for ($i=0; $i < $photoCount; $i++) { 	 	
				$name = $_FILES['photo']['name'][$i];
			 	$name_array = explode('.' , $name);
			 	$filename = $name_array[0];
			 	$file_extension = $name_array[1];
			 	$mime = explode('/', $_FILES['photo']['type'][$i]);
			 	$mime_type = $mime[0];
			 	$mime_extension = $mime[1];
			 	$tmpLoc[] = $_FILES['photo']['tmp_name'][$i];
			 	$fileSize = $_FILES['photo']['size'][$i];
			 	$upload_name = md5(microtime().$i).'.'.$file_extension;
			 	$upload_location[] = BASEURL.'images/products/'.$upload_name;
			 	if($i != 0){
			 		$db_path .= ',';
			 	}
			 	$db_path .= '/php/shopping_cart_2/images/products/'.$upload_name;

			 	//check for errors and limits
				if ($mime_type != 'image') {
					$errors[] = 'The file must be an image';
				}
				if ($fileSize > 10000000) {
					$errors[] = 'The file must be less than 10MB';
				}
				if ($file_extension != $mime_extension && ($mime_extension == 'jpeg' && $file_extension != 'jpg')) {
					$errors[] = 'File extension does not match the file';
				}
				if (!in_array($file_extension, $allowed)) {
					$errors[] = 'The file extension must be a png, jpg, jpeg, or gif file';
			 	}
			}
		}
		if (!empty($errors)) {
			echo display_errors($errors);
		} else {
			//update database
			 if ($photoCount > 0) {
			 	for ($i=0; $i < $photoCount; $i++) { 
			 		move_uploaded_file($tmpLoc[$i], $upload_location[$i]);
			 	}
			// 	
			 }
			$insertSql = ("INSERT INTO products (title, price, list_price, brand, categories, img, description, featured, colors_available, deleted) VALUES ('$title', '$price', '$list_price', '$brand', '$categoy', '$db_path', '$description', '0', '$colors', '0')");
			if (isset($_GET['edit'])) {
				$insertSql = ("UPDATE products SET title = '$title', price = '$price', list_price = '$list_price', brand = '$brand' categories = '$category' , img = '$db_path', description = $description, colors_available = $colors WHERE id = '$edit_id'");
			}
			$db -> query($insertSql);
			header('Location: products.php');
			//var_dump($insertSql);
		}

	}

	?>
	<h2 class="text-center"><?=((isset($_GET['edit']))?'Edit ': 'Add a new ');?>Product</h2>
	<hr>
	<form action="products.php?<?=((isset($_GET['edit']))?'edit='. $edit_id : 'add=1');?>" method="POST" enctype="multipart/form-data">
		<div class="form-group col-md-3">
			<label for="title">* Title</label>
			<input type="text" name="title" class="form-control" id="title" value="<?=$title?>">
		</div>
		<div class="form-group col-md-3">
			<label for="brand">* Brand</label>
			<select name="brand" id="brand" class="form-control">
				<option value=""<?=(($brand == '')?' selected':'')?>"></option>
				<?php while($b = mysqli_fetch_assoc($brand_query)): ?>
					<option value="<?=$b['id'];?>"<?=(($brand == $b['id'])?' selected':'')?>><?=$b['brand']?></option>
				<?php endwhile ?>
			</select>
		</div>
		<div class="form-group col-md-3">
			<label for="parent">* Parent Category</label>
			<select name="parent" id="parent" class="form-control">
				<option value=""<?=(($parent == '')?' selected':'')?>></option>
				<?php while($p = mysqli_fetch_assoc($parent_query)): ?>
					<option value="<?=$p['id'];?>"<?=(($parent == $p['id'])?' selected':'')?>><?=$p['category']?></option>
				<?php endwhile ?>
			</select>
		</div>
		<div class="form-group col-md-3">
			<label for="child">* Child Category</label>
			<select name="child" id="child" class="form-control">
				
			</select>
		</div>
		<div class="form-group col-md-3">
			<label for="price">* Price</label>
			<input type="text" id="price" name="price" class="form-control" value="<?=$price?>">
		</div>
		<div class="form-group col-md-3">
			<label for="list_price">List Price</label>
			<input type="text" id="list_price" name="list_price" class="form-control" value="<?=$list_price?>">
		</div>
		<div class="form-group col-md-3">
			<label>Colors &amp; Quantity</label>
			<button type="button" class="btn btn-info btn-md form-control" onclick="jQuery('#colorModal').modal('toggle');return false;">Set Quantity and Colors</button>
		</div>
		<div class="form-group col-md-3">
			<label for="colors">* Quantity &amp; Colors Preview</label>
			<input type="text" class="form-control" name="colors" id="colors" value="<?=$colors?>" readonly>
		</div>
		<div class="form-group col-md-6">
			<div class="col-md-6">
				<label for="photo">Product Photo</label>
				<input type="file" name="photo[]" id="photo" class="form-control" multiple>
			</div>
			<div class="col-md-6">
				<?php if ($saved_image != '') : ?>
					<?php 
						$imgi = 1;
						$images = explode(',',$saved_image);?>
					<?php foreach($images as $image): ?>
					<div class="saved-image col-md-4">
						<img src="<?=$image?>" class="img-responsive" alt="saved_image">
						<a href="products.php?delete_image=1&edit=<?=$edit_id?>&imgi=<?=$imgi;?>" class="text-danger">Delete Image</a>
					</div>
				<?php 
					$imgi++;
					endforeach; 
				?>
				<?php endif ?>
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="description">Product Description</label>
			<textarea class="form-control" name="description" id="description" rows="6"><?=$description?></textarea>
		</div>
		<div class="form-group pull-right">
			<a href="products.php" class="btn btn-default">Cancel</a>
			<input type="submit" value="<?=((isset($_GET['edit']))?'Edit ': 'Add a new ');?>Product" class="btn btn-success pull-right">
		</div>
	</form>

	<!-- COLORS MODAL -->
	<!-- Modal -->
	<div id="colorModal" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title">Colors and Quantity</h3>
				</div>

				<div class="modal-body">
					<div class="container-fluid">
						<?php for($i = 1; $i <= 6; $i++) : ?>
							<div class="form-group col-md-4">
								<label for="color<?=$i;?>">Color: <?=$i?></label>
								<input type="text" name="color<?=$i;?>" id="color<?=$i;?>" value="<?=((!empty($c_array[$i-1]))?$c_array[$i-1]:'')?>" class="form-control">
							</div>
							<div class="form-group col-md-2">
								<label for="qty<?=$i;?>">Quantity: </label>
								<input type="number" name="qty<?=$i;?>" id="qty<?=$i;?>" value="<?=((!empty($q_array[$i-1]))?$q_array[$i-1]:'')?>" min="0" class="form-control">						
							</div>
						<?php endfor; ?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="updateColors();$('#colorModal').modal('toggle');return false;">Save</button>
				</div>
			</div>

		</div>
	</div>

	<?php } else {

		$sql = "SELECT * FROM products WHERE deleted = 0";
		$product_results = $db->query($sql);
		if (isset($_GET['featured'])) {
			$id = (int)$_GET['id'];
			$featured = (int)$_GET['featured'];
			$featured_sql = "UPDATE products SET featured = '$featured' WHERE id = '$id'";
			$db->query($featured_sql);
			header('Location: products.php');
		}
		?>
		<h2 class="text-center">Products</h2>
		<a href="products.php?add=1" class="btn btn-success pull-right" id="add_product_button">Add Product</a><div class="clearfix"></div>
		<hr>
		<table class="table table-bordered table-condensed table-striped text-center myTable">
			<thead>
				<th class="text-center">Edit | Delete</th>
				<th class="text-center">Product</th>
				<th class="text-center">Price</th>
				<th class="text-center">Category</th>
				<th class="text-center">Featured</th>
				<th class="text-center">Deleted</th>
			</thead>
			<tbody>
				<?php while($product = mysqli_fetch_assoc($product_results)) :
				$childID = $product['categories'];
				$child_sql = "SELECT * FROM categories WHERE id = '$childID'";
				$child_result = $db->query($child_sql);
				$child = mysqli_fetch_assoc($child_result);

				$parentID = $child['parent'];
				$parent_sql = "SELECT * FROM categories where id = '$parentID'";
				$parent_result = $db->query($parent_sql);
				$parent = mysqli_fetch_assoc($parent_result);
				$category = $parent['category'].'~'.$child['category'];
				?>
				<tr>
					<td>
						<a href="products.php?edit=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a><span> | </span>
						<a href="products.php?delete=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a>
					</td>
					<td><?php echo $product['title']; ?></td>
					<td><?php echo money($product['price']); ?></td>
					<td><?php echo $category; ?></td>
					<td>
						<a href="products.php?featured=<?=(($product['featured']==0)?'1':'0');?>&id=<?=$product['id'];?>" class="btn btn-xs btn-default">
							<span class="glyphicon glyphicon-<?=(($product['featured']==1)?'minus':'plus');?>"></span>
						</a>&nbsp;<?=(($product['featured'] == 1)?'Featured Product':'');?>
					</td>
					<td><?php echo $product['deleted']; ?></td>
				</tr>
			<?php endwhile; ?>	
		</tbody>
	</table>
	<?php } ?>

	<?php include 'includes/footer.php'; ?>
	<script>
		$('document').ready(function(){
			get_child_options('<?=$category?>');
		});
	</script>