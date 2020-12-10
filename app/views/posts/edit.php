<?php require APPROOT . '/views/inc/header.php'; ?>


	<a href="<?php echo URLROOT; ?>/posts" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
	<div class="card card-body bg-light mt-5">
		<h2>Edit Post</h2>

		<img src="
			<?php
				// Create a base64 entity of image loaded 
				$path = $data['userimage_path'];
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$imgdata = file_get_contents($path);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($imgdata);
				echo $base64;
			?>" alt="Failed to Retreive Image" class="img-fluid";/>

		<p>Edit a post with this form</p>
		<form action="<?php echo URLROOT; ?>/posts/edit/<?php echo $data['id']; ?>" method="post">
			<div class="form-group">
				<label for="title">Title: <sup>*</sup></label>
				<input type="text" name="title" class="form-control form-control-lg <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['title']; ?>">
				<span class="invalid-feedback"><?php echo $data['title_err']; ?></span>
			</div>
			<div class="form-group">
				<label for="body">Body: <sup>*</sup></label>
				<textarea name="body" class="form-control form-control-lg <?php echo (!empty($data['body_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['body']; ?></textarea>
				<span class="invalid-feedback"><?php echo $data['body_err']; ?></span>
			</div>
			<input type="submit" class="btn btn-success" value="Submit">
		</form>
	</div>
<script src="<?php echo URLROOT; ?>/js/cam.js"></script>
<?php require APPROOT . '/views/inc/footer.php'; ?>