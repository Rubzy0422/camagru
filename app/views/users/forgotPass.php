<?php require APPROOT . '/views/inc/header.php';?>
<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Forgot Password</h2>
			<form action="<?php echo URLROOT; ?>/users/forgotPass" method="post">
				<div class="form-group">
					<label for="email">Email: <sup>*</sup></label>
					<input type="text" name="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
					<span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
				</div>
				
				<div class="row">
					<div class="col">
						<input type="submit" value="Send Reset Link" class="btn btn-success btn-block">
					</div>
				</div>

			</form>
		</div>
	</div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>