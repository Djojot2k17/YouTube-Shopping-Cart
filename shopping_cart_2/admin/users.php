	<?php 
require_once '../system/init.php';
if (!isLoggedIn()) {
	loginErrorRedirect();
}
if (!hasPermission('admin')) {
	permissionErrorRedirect('index.php');
}
include 'includes/head.php';
include 'includes/navigation.php';
if (isset($_GET['delete'])) {
	$delete_id = sanitize($_GET['delete']);
	$db->query("DELETE FROM users WHERE id = '$delete_id'");
	$_SESSION['success_flash'] = 'User has been deleted';
	header('Location: users.php');
}

if (isset($_GET['add']) || isset($_GET['edit'])) {
	$name = ((isset($_POST['name']) && $_POST['name'] != '')?sanitize($_POST['name']):'');
	$email = ((isset($_POST['email']) && $_POST['email'] != '')?sanitize($_POST['email']):'');
	$password = ((isset($_POST['password']) && $_POST['password'] != '')?sanitize($_POST['password']):'');
	$confirm = ((isset($_POST['confirm']) && $_POST['confirm'] != '')?sanitize($_POST['confirm']):'');
	$permissions = ((isset($_POST['permissions']) && $_POST['permissions'] != '')?sanitize($_POST['permissions']):'');
	$errors = array();
	
	if (isset($_GET['edit'])) {
		$edit_id = (int)$_GET['edit'];
		$user_results = $db->query("SELECT * FROM users WHERE id= '$edit_id'");
		$users = mysqli_fetch_assoc($user_results);

		$name = ((isset($_POST['name']) && !empty($_POST['name']))?sanitize($_POST['name']):$users['full_name']);
		$email = ((isset($_POST['email']) && !empty($_POST['email']))?sanitize($_POST['email']):$users['email']);
		$permissions = ((isset($_POST['permissions']) && !empty($_POST['permissions']))?sanitize($_POST['permissions']):$users['permissions']);
		var_dump($_POST);
	}
	if ($_POST) {
		$emailQuery = $db->query("SELECT * FROM users WHERE email = '$email'");
		$emailCount = mysqli_num_rows($emailQuery);
		if ($emailCount != 0) {
			$errors[] = 'That email already exists in our database.';
		}
		$required = array('name', 'email', 'password', 'confirm', 'permissions');
		foreach ($required as $f) {
			if(empty($_POST[$f])){
				$errors[] = 'You must fill out all fields.';	
				break;
			}				
		}
		// check if password is less than 6 characters
		if(strlen($password) < 6){
			$errors[] = 'Your password must be at least 6 characters.';
		}
		// make sure passwords match
		if($password != $confirm){
			$errors[] = 'Your passwords do not match.';
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'You must enter a valid email.';
		}
		if(!empty($errors)){
			echo display_errors($errors);
		} 
		if ($_GET['add']) {
			//add user to database
			$hashed = password_hash($password, PASSWORD_DEFAULT);
			$db->query("INSERT INTO users (full_name, email, password, permissions) VALUES('$name','$email','$hashed', '$permissions')");
			$_SESSION['success_flash'] = 'User has been added.';
			header('Location: users.php');
		} 
		if ($_GET['edit']) {
			//edit user in database
			$db->query("UPDATE users SET full_name = '$name', email = '$email', permissions = '$permissions' WHERE id = '$edit_id'");
			$_SESSION['success_flash'] = 'User has been edited.';
			header('Location: users.php');
		}
	}   ?>
	<?php if(isset($_GET['add'])) : ?>
		<h2 class="text-center">Add New User</h2>
		<form action="users.php?add=1" method="POST" id="userForm">
			<div class="form-group">
				<label for="name">Full Name: </label>
				<input type="text" name="name" id="name" class="form-control" value="<?=$name;?>">
			</div>
			<div class="form-group">
				<label for="email">Email: </label>
				<input type="email" name="email" id="email" class="form-control" value="<?=$email;?>">
			</div>
			<div class="form-group">
				<label for="password">Password: </label>
				<input type="password" name="password" id="password" class="form-control" value="<?=$password;?>">
			</div>
			<div class="form-group">
				<label for="confirm">Confirm Password: </label>
				<input type="password" name="confirm" id="confirm" class="form-control" value="<?=$confirm;?>">
			</div>
			<div class="form-group">
				<label for="permissions">Permissions: </label>
				<select class="form-control" name="permissions">
					<option value=""<?=(($permissions == '')?' selected':'');?>></option>
					<option value="editor"<?=(($permissions == 'editor')?' selected':'');?>>Editor</option>
					<option value="admin,editor"<?=(($permissions == 'admin,editor')?' selected':'');?>>Admin</option>
				</select>
			</div>
			<div class="form-group text-right">
				<a href="users.php" class="btn btn-default">Cancel</a>
				<input type="submit" value="Add a New User" class="btn btn-primary">
			</div>
		</form>
	<?php endif; ?>
	<?php if(isset($_GET['edit'])) : ?>
		<h2 class="text-center">Edit User</h2>
		<form action="users.php?edit=<?=$edit_id?>" method="POST" id="userForm">
			<div class="form-group">
				<label for="name">Full Name: </label>
				<input type="text" name="name" id="name" class="form-control" value="<?=$name;?>">
			</div>
			<div class="form-group">
				<label for="email">Email: </label>
				<input type="email" name="email" id="email" class="form-control" value="<?=$email;?>">
			</div>
			<div class="form-group">
				<label for="permissions">Permissions: </label>
				<select class="form-control" name="permissions">
					<option value=""<?=(($permissions == '')?' selected':'');?>></option>
					<option value="editor"<?=(($permissions == 'editor')?' selected':'');?>>Editor</option>
					<option value="admin,editor"<?=(($permissions == 'admin,editor')?' selected':'');?>>Admin</option>
				</select>
			</div>
			<div class="form-group">
				<a href="users.php" class="btn btn-default btn-block">Cancel</a>
				<input type="submit" value="Edit user" class="btn btn-primary btn-block">
			</div>
		</form>
	<?php endif; ?>

	<?php } else { $userQuery = $db->query("SELECT * FROM users ORDER BY full_name"); ?>

	<h2 class="text-center">Users</h2>
	<a href="users.php?add=1" class="btn btn-success pull-right" id="add-user-btn">Add New User</a>
	<hr>
	<table class="table table-bordered table-striped table-condensed text-center myTable">
		<thead>
			<th class="text-center">Edit | Delete</th>
			<th class="text-center">Name</th>
			<th class="text-center">Email</th>
			<th class="text-center">Join Date</th>
			<th class="text-center">Last Login</th>
			<th class="text-center">Permissions</th>
		</thead>
		<tbody>
			<?php while($user = mysqli_fetch_assoc($userQuery)): ?>
				<tr>
					<td>
						<?php if($user['id'] != $user_data['id']) : ?>
							<a href="users.php?edit=<?=$user['id'];?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a><span> | </span>
							<a href="users.php?delete=<?=$user['id'];?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove-sign"></span></a>					
						<?php endif; ?>
						<?php if($user['id'] == $user_data['id']) : ?>
							<span>Admin</span>
						<?php endif; ?>
					</td>
					<td><?=$user['full_name']?></td>
					<td><?=$user['email']?></td>
					<td><?=prettyDate($user['join_date'])?></td>
					<td><?=(($user['last_login'] == '0000-00-00 00:00:00')?'Never ':prettyDate($user['last_login']))?></td>
					<td><?=$user['permissions']?></td>
				</tr>
			<?php endwhile; ?>
		</tbody>
	</table>

	<?php
}
include 'includes/footer.php'; 
?>