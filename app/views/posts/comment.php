<?php require APPROOT . '/views/inc/header.php'; ?>
<!-- Go back to posts -->
<a href="<?php echo URLROOT; ?>/posts" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>

<br>
<div class="card card-body mb-3">
	<h4 class="card-title"><?php echo $data['post']->title; ?></h4>
		<div class="bg-light p-2 mb-3">
			Written by <?php echo $data['user']->uname; ?> 
			</div>
			<img src="
			<?php
				// Create a base64 entity of image
				$path = $data['post']->userimage_path;
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$imgdata = file_get_contents($path);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($imgdata);
				echo $base64; 
			?>" alt="Failed to Retreive Image" class="img-fluid";/>

			<p class="card-text"><?php echo $data['post']->body; ?></p>
			
			<div class="row">

				<div class="col">
					<a href="<?php echo URLROOT; ?>/posts/like/<?php echo $data['post']->postId; ?>" class="btn btn-dark">
					<i class="fa fa-heart"></i> <?php echo ($data['post']->likes != 0) ?></a>
				</div>
				<?php if(isLoggedIn()) : ?>
					<?php if($data['post']->userid == $_SESSION['userid']) : ?>

						<a href="<?php echo URLROOT; ?>/posts/edit/<?php echo $data['post']->postId; ?>" class="btn btn-dark">Edit</a>
						<form class="pull-right" action="<?php echo URLROOT; ?>/posts/delete/<?php echo $data['post']->postId; ?>" onsubmit="return confirm('are you sure you want to delete this post?');" method="post">
							<input type="submit" value="Delete" class="btn btn-danger">
						</form>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
</div>

<div class="container">
	<div class="card card-body mb-3">
		<div class="row">
			<!-- Actual comments left -->
			<div class="col">
				<?php foreach ($data['comments'] as $comment ) : ?>
					<div class="card card-body">
						<h4 class="header"><?php echo $comment->uname; ?></h4>

						<p><?php echo $comment->comment; ?></p>
					</div>
					
				<?php endforeach; ?>
			</div>
			<!-- Form Right -->
			<div class="col">
				<form action="<?php echo URLROOT; ?>/posts/comment/<?php echo $data['post']->postId; ?>" method="post">
					<div class="form-group">
						<label for="userpost">Comment: <sup>*</sup></label>
						<textarea name="userpost" class="form-control form-control-lg <?php echo (!empty($data['userpost_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['userpost']; ?></textarea>
						<span class="invalid-feedback"><?php echo $data['userpost_err']; ?></span>
					</div>
					<input type="submit" class="btn btn-success" value="Submit">
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Loop through comments received and display them -->


<?php require APPROOT . '/views/inc/footer.php'; ?>