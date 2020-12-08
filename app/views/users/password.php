<!-- Only require this if user is logged in lol  -->
<?php 
	if ($data['display'] == true)
	{
		require APPROOT . '/views/inc/header.php';
	}
	else 
	{
		echo '
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">	
		<div class="container">
		';
	}
?>

<!-- Actual Update of Password -->
<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Update Password</h2>
			<form action="<?php echo URLROOT; ?>/users/updatePass<?php if (isset($data['token'])) echo "/" . $data['token']; ?>" method="post">
			<?php 
				if ($data['display'] == true)
				{
					echo '<div class="form-group">';
					echo '<label for="name">Old Password: <sup>*</sup></label>';
					echo '<input type="password" name="old_password" class="form-control form-control-lg ';
					echo (!empty($data['old_password_err'])) ? 'is-invalid' : '';
					echo ' value="';
					echo $data['old_password'];
					echo '">';
					echo '<span class="invalid-feedback">';
					echo $data['old_password_err'];
					echo '</span>';
					echo '</div>';
				}
			?>
				<div class="form-group">
					<label for="password">New Password: <sup>*</sup></label>
					<input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
					<span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
				</div>
				<div class="form-group">
					<label for="confirm_password">New Password Confirm: <sup>*</sup></label>
					<input type="password" name="confirm_password" class="form-control form-control-lg <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
					<span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
				</div>
				<div class="row">
					<div class="col">
						<input type="submit" value="Update Password" class="btn btn-success btn-block">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Only require this if user is logged in lol  -->

<?php 
	if ($data['display'] == true)
	{
		require APPROOT . '/views/inc/footer.php';
	}
	else 
	{
		echo '</div>';
	}
?>
