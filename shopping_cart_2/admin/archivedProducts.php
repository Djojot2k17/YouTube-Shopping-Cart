<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/php/shopping_cart_2/system/init.php';
if (!isLoggedIn()) {
	loginErrorRedirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

$sql = "SELECT * FROM products WHERE deleted = '1'";
$product_results = $db->query($sql);
if (isset($_GET['restore']) && !empty($_GET['restore'])) {
	$id = $_GET['restore'];
	$restoreSql = "UPDATE products SET deleted = '0' WHERE  id = '$id'";
	//var_dump($restoreSql);
	$db->query($restoreSql);
	header('Location: archivedProducts.php');
}
?>

<h2 class="text-center">Archived Products</h2>
<hr>
<table class="table table-bordered table-condensed table-striped text-center myTable">
	<thead>
		<th></th>
		<th class="text-center">Product</th>
		<th class="text-center">Price</th>
		<th class="text-center">Category</th>
		<th class="text-center">Featured</th>
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
				<a href="archivedProducts.php?restore=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-export"></span>Restore Product</a>
			</td>
			<td><?php echo $product['title']; ?></td>
			<td><?php echo money($product['price']); ?></td>
			<td><?php echo $category; ?></td>
			<td><?php echo $product['featured']; ?></td>
		</tr>
	<?php endwhile; ?>	
</tbody>
</table>
