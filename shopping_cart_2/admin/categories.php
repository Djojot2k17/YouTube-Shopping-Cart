<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/php/shopping_cart_2/system/init.php';
if (!isLoggedIn()) {
	loginErrorRedirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

$sql = "SELECT * FROM categories WHERE parent = 0";
$result = $db->query($sql);
$errors = array();
$category = '';
$post_parent = '';

	// Edit Category

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
	$edit_id = (int)$_GET['edit'];
	$edit_id = sanitize($edit_id);
	$edit_sql = "SELECT * FROM categories WHERE id = '$edit_id'";
	$edit_result = $db->query($edit_sql);
	$edit_category = mysqli_fetch_assoc($edit_result);
}

	// Delete Category
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
	$delete_id = (int)$_GET['delete'];
	$delete_id = sanitize($delete_id);

		 // $sql = "SELECT * FROM categories WHERE id = '$delete_id'";
			//  $result = $db->query($sql);
			//  $category = mysqli_fetch_assoc($result);
			//  if($category['parent'] == 0){
			// 		$sql = "DELETE FROM categories WHERE parent = '$delete_id";
			// 		$db->query($sql);
			//  }	

			 // This is another way of doing what $delete_sql is doing	

	$delete_sql = "DELETE FROM categories WHERE id = '$delete_id' OR parent = '$delete_id'";
	$db->query($delete_sql);
	header('Location: categories.php');
}

	// Process Form

if (isset($_POST) && !empty($_POST)) {
			// Check if category is blank
	$post_parent = sanitize($_POST['parent']);
	$category = sanitize($_POST['category']);
	$sql_form = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent'";
	$form_result = $db->query($sql_form);
	$count = mysqli_num_rows($form_result);
		//var_dump($post_parent);
			// If category is blank
	if ($category == '') {
		$errors[] .= 'The category cannot be left blank.';
	}

			// If category exists in the database
	if ($count > 0) {
		$errors[] .= $category . ' already exists. Please choose a new category';
	}
		// Display errors or update database
	if (!empty($errors)) {
			//display errors
		$display = display_errors($errors); ?>
		<script>
			jQuery('document').ready(function(){
				$('#errors').html('<?= $display ?>');
			});
		</script>
		<?php 
	} else {
			//update database
		$update_sql = "INSERT INTO categories (category, parent) VALUES ('$category','$post_parent')";
		if (isset($_GET['edit'])) {
			$update_sql = "UPDATE categories SET category = '$category', parent = '$post_parent' WHERE id = '$edit_id'";
		}
		$db->query($update_sql);
		header('Location: categories.php');
	}
}
	// set category value to edit button
$category_value = '';
$parent_value = 0;
if (isset($_GET['edit'])) {
	$category_value = $edit_category['category'];
	$parent_value = $edit_category['parent'];
} else {
	if (isset($_POST)) {
		$category_value = $category;
		$parent_value = $post_parent;
	}
}
?>

<h2 class="text-center">Categories</h2>
<hr>
<!--Add/Edit Category Form -->
<div class="categoryForm">
	<div class="row">
		<div class="col-md-4">
			<form action="categories.php<?=((isset($_GET['edit']))?'?edit='.$edit_id:'');?>" method="post" >
				<legend><?=((isset($_GET['edit']))?'Edit ':'Add a '); ?>Category</legend>
				<div id="errors"></div>
				<div class="form-group">
					<label for="parent">Parent</label>
					<select class="form-control" name="parent" id="parent">
						<option value="0"<?=(($parent_value == 0)?'selected="selected"':'');?>>Parent</option>
						<?php while($parent = mysqli_fetch_assoc($result)) : ?>
							<option value="<?=$parent['id'];?>"<?=(($parent_value == $parent['id'])?' selected="selected"':'');?>><?= $parent['category']; ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="category">Category</label>
					<input type="text" class="form-control" id="category" name="category" 
					value="<?= $category_value ?>">
				</div>
				<div class="form-group">
					<input type="submit" value="<?=((isset($_GET['edit']))?'Edit Category':'Add Category'); ?>" class="btn btn-success">
				</div>
			</form>
		</div>
		<!-- Category Table -->
		<div class="col-md-8">
			<table class="table table-bordered table-condensed text-center myTable">
				<thead>
					<th class="text-center">Category</th>
					<th class="text-center">Parent</th>
					<th class="text-center">Edit / Delete</th>
				</thead>
				<tbody>
					<?php
					$sql = "SELECT * FROM categories WHERE parent = 0";
					$result = $db->query($sql); 
					while($parent = mysqli_fetch_assoc($result)):
						$parent_id = (int)$parent['id'];
					$sql2 = "SELECT * FROM categories WHERE parent = $parent_id";
					$category_result = $db->query($sql2);
					?>

					<tr class="bg-primary">
						<td><?=$parent['category'];?></td>
						<td>Parent</td>
						<td>
							<a href="categories.php?edit=<?=$parent['id']?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
							<a href="categories.php?delete=<?=$parent['id']?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>
						</td>
					</tr>
					<?php while($child = mysqli_fetch_assoc($category_result)): ?>
						<tr class="bg-info">
							<td><?=$child['category'];?></td>
							<td><?=$parent['category']; ?></td>
							<td>
								<a href="categories.php?edit=<?=$child['id']?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
								<a href="categories.php?delete=<?=$child['id']?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>
							</td>
						</tr>
					<?php endwhile; ?>
				<?php endwhile; ?>
			</tbody>
		</table>
	</div>
</div>
</div>

<?php include 'includes/footer.php'; ?>