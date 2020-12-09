<?php require APPROOT . '/views/inc/header.php'; ?>
	<div class="row">
		<div class="col-md-6 mx-auto">
			<div class="card card-body bg-light mt-5">
				<h2>Update your Account</h2>
				<p>Please fill out this form to update your account</p>
				<form action="<?php echo URLROOT; ?>/users/update" method="post">
					<div class="form-group">
						<label for="name">Uname: <sup>*</sup></label>
						<input type="text" name="uname" class="form-control form-control-lg <?php echo (!empty($data['uname_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['uname']; ?>">
						<span class="invalid-feedback"><?php echo $data['uname_err']; ?></span>
					</div>
					<div class="form-group">
						<label for="email">Email: <sup>*</sup></label>
						<input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
						<span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col">
								<label for="notification">Enable Notifications:</label>
							</div>
							<div class="col">
								<input type="checkbox" id="notification" name="notification" value="true" <?php if ($data['notifications'] == true) echo 'checked'; ?>>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col">
							<input type="submit" value="Update" class="btn btn-success btn-block">
						</div>
						<div class="col">
							<a href="<?php echo URLROOT;?>/users/updatePass" class="btn btn-success btn-block">Update Password</a>
						</div>
					</div>
				</form>
				
				<div class="row" style="margin-top: 10%">
						<div class="col"></div>
						<div class="col">
							<form action="<?php echo URLROOT; ?>/users/delete" method="post" onsubmit="return confirm('are you sure you want to delete your account?');">
								<input type="submit" value="Delete Account" class="btn btn-danger btn-block">
							</form>
						</div>
				</div>
			</div>
		</div>
	</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>