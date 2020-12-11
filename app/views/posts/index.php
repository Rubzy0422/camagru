<?php require APPROOT . '/views/inc/header.php'; ?>
	<?php flash('post_message'); ?>
	<div class="row mb-3">
		<div class="col-md-6">
			<h1>Posts</h1>
		</div>
		<div class="col-md-6">
			<a href="<?php echo URLROOT; ?>/posts/add" class="btn btn-primary pull-right">
				<i class="fa fa-plus"></i> Add Post
			</a>
		</div>
	</div>

	<?php foreach($data['posts'] as $post) : ?>
		<div class="card card-body mb-3">
			<h4 class="card-title"><?php echo $post->title; ?></h4>
			<div class="bg-light p-2 mb-3">
				Written by <?php echo $post->uname; ?> on <?php echo $post->postCreated; ?>
			</div>

			<img src="
			<?php
				// Create a base64 entity of image
				$path = $post->userimage_path;
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$data = file_get_contents($path);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
				echo $base64; 
			?>" alt="Failed to Retreive Image" class="img-fluid";/>

			
			<p class="card-text"><?php echo $post->body; ?></p>
			
			<div class="row">
				<div class="col">
					<!-- Work with Likes Table to add / Remove like lol -->
					
					<a href="<?php echo URLROOT; ?>/posts/like/<?php echo $post->postId; ?>" class="btn btn-dark">
					<i class="fa fa-heart"></i> <?php echo ($post->likes != 0) ? $post->likes :''  ?></a>
				</div>
				<div class="col">
					<a href="<?php echo URLROOT; ?>/posts/comment/<?php echo $post->postId; ?>" class="btn btn-dark">
					<i class="fa fa-comment"></i></a>
				</div>
				<?php if(isLoggedIn()) : ?>
					<?php if($post->userid == $_SESSION['userid']) : ?>

						<a href="<?php echo URLROOT; ?>/posts/edit/<?php echo $post->postId; ?>" class="btn btn-dark">Edit</a>
						<form class="pull-right" action="<?php echo URLROOT; ?>/posts/delete/<?php echo $post->postId; ?>" onsubmit="return confirm('are you sure you want to delete this post?');" method="post">
							<input type="submit" value="Delete" class="btn btn-danger">
						</form>
					<?php endif; ?>
				<?php endif; ?>
			</div>

		</div>
	<?php endforeach; ?>



	
<?php require APPROOT . '/views/inc/footer.php'; ?>