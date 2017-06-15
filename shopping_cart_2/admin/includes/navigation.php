<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<a href="/php/shopping_cart_2/admin/index.php" class="navbar-brand">Admin</a>
		<ul class="nav navbar-nav">
			<!-- Menu Items -->	
			<!-- <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#"></a></li>
				</ul>
			</li> -->
			<li><a href="brands.php">Brands</a></li>
			<li><a href="categories.php">Categories</a></li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Products<span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					<li><a href="products.php">Live Products</a></li>
					<li><a href="archivedProducts.php">Archived Products</a></li>
				</ul>
			</li>
			<?php if(hasPermission('admin')): ?>
				<li><a href="users.php">Users</a></li>
			<?php endif; ?>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Hello <?=$user_data['first'];?>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu">
					<li><a href="change_password.php">Change Password</a></li>
					<li><a href="logout.php">Log Out</a></li>
				</ul>
			</li>
		</ul>
	</div>
</nav>