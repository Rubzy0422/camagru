<?php
	class Posts extends Controller {
		public function __construct(){
			$this->postModel = $this->model('Post');
			$this->userModel = $this->model('User');
			$this->likeModel = $this->model('Like');
			$this->commentModel = $this->model('Comment');
		}

		public function index($page = 1){
			// Get posts
			// Add Pagination Page number


			$posts = $this->postModel->getPosts((int)$page);
			
			// Loop through posts and add likes
			$data = [
				'posts' => $posts,
			];
			$this->view('posts/index', $data);
		}

		public function add(){
			if(!isLoggedIn()){
				redirect('users/login');
			}
			$prevPosts = [];
			$stickers = glob('../public/stickers/*.{jpg,png,gif}', GLOB_BRACE);

			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				// Sanitize POST array
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
				$data = [
					'title' => trim($_POST['title']),
					'body' => trim($_POST['body']),
					'userid' => $_SESSION['userid'],
					'userimg' => $_POST['userimg'],
					'stickerimg' => $_POST['stickerimg'],
					'stickers' => $stickers,
					'prev_posts' => $prevPosts,
					'title_err' => '',
					'body_err' => '',
					'stickerimg_err' => '',
					'userimg_err' => ''
				];

				// // Validate data
				if(empty($data['title'])){
					$data['title_err'] = 'Please enter title';
				}
				if(empty($data['body'])){
					$data['body_err'] = 'Please enter body text';
				}
				if (empty($data['stickerimg'])) {
					$data['stickerimg_err'] = 'Please enter a sticker on the image';
				}
				if (empty($dara['userimg_err'])) {
					$data['userimg_err'] = 'Please enter a image of your chosing';
				}
				// Make sure no errors
				if(empty($data['title_err']) && empty($data['body_err']) && empty($data['stickerimg_err'] && empty($data['userimg_err']))) {
					if($this->postModel->addPost($data)){
						flash('post_message', 'Post Added');
						redirect('posts');
					} else {
						//Image not created or what ever lol
						die('Something went wrong');
					}
				} else {
					$this->view('posts/add', $data);
				}

			} else {
				$data = [
					'title' => '',
					'body' => '',
					'userid' => '',
					'userimg' => '',
					'stickerimg' => '',
					'stickers' => $stickers,
					'prev_posts' => $prevPosts,
					'title_err' => '',
					'body_err' => '',
					'stickerimg_err' => '',
					'userimg_err' => ''
				];
	
				$this->view('posts/add', $data);
			}
		}

		public function edit($id){
			if(!isLoggedIn()){
				redirect('users/login');
			}
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				// Sanitize POST array
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

				$data = [
					'id' => $id,
					'title' => trim($_POST['title']),
					'body' => trim($_POST['body']),
					'userid' => $_SESSION['userid'],
					'title_err' => '',
					'body_err' => ''
				];

				// Validate data
				if(empty($data['title'])){
					$data['title_err'] = 'Please enter title';
				}
				if(empty($data['body'])){
					$data['body_err'] = 'Please enter body text';
				}

				// Make sure no errors
				if(empty($data['title_err']) && empty($data['body_err'])){
					// Validated
					if($this->postModel->updatePost($data)){
						flash('post_message', 'Post Updated');
						redirect('posts');
					} else {
						die('Something went wrong');
					}
				} else {
					// Load view with errors
					$this->view('posts/edit', $data);
				}

			} else {
				// Get existing post from model
				$post = $this->postModel->getPostById($id);

				// Check for owner
				if($post->userid != $_SESSION['userid']){
					redirect('posts');
				}
				$data = [
					'id' => $id,
					'title' => $post->title,
					'body' => $post->body,
					'userimage_path' => $post->userimage_path
				];
				$this->view('posts/edit', $data);
			}
		}

		public function comment($id){
			// Display comments :)
			$post = $this->postModel->getPostById($id);
			$user = $this->userModel->getUserById($post->userid);
			$comments = $this->commentModel->getCommentForId($post->postId);

			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
				
				$data = [
					'post' => $post,
					'user' => $user,
					'comments' => $comments,
					'userpost' => trim($_POST['userpost']),
					'userpost_err' => ''
				];

				if (empty($data['userpost']))
				{
					$data['userpost_err'] = 'Please enter a comment';
				}
				if(empty($data['userpost_err']))
				{
					// Actual Update Of comments 
					$userdata['userid'] = $_SESSION['userid'];
					$userdata['postid'] = $id;
					$userdata['comment'] = $data['userpost'];
					$data['userpost'] = '';
					
					if ($user->notifications == true)
					{
						$email_data['username'] = $user->uname;
						$email_data['action'] = "Comment";
						$email_data['post_title'] = $post->title;
						$email_data['post_url'] = URLROOT . '/posts/index/';

						if (!send_mail($user->email, $email_data))
							die ('Could not Send EMAIL!');
					}

					if ($updatedComment = $this->commentModel->addComment($userdata))
					{
						$data['comments'] = $updatedComment;
					}
					else {
						die ("Could not update Comments!");
					}
				}			
				$this->view('posts/comment', $data);
			}
			else {
				
				$data = [
					'post' => $post,
					'user' => $user,
					'comments' => $comments,
					'userpost' => '',
					'userpost_err' => ''
				];
	
				$this->view('posts/comment', $data);
			}
		}

		public function commentDelete($id) {
			// Comment Id, Post Id 
				$data = explode('|', $id);
				if (count($data) != 2)
				{
					// Flash error
					redirect('posts');
				}
				// if post does not exist 
				$comment = $this->commentModel->getCommentById($data[0]);
				$post = $this->postModel->getPostById($data[1]);

				if ($post->postId == NULL)
				{
					redirect('posts');
				}

				if ($comment == false)
				{
					redirect('posts/comment/' . $data[1]);
				}

				var_dump($comment);
				die();
				// Creator of post or commenter
							//|| $_SESSION['userid'] == $comment->
				if ($_SESSION['userid'] == $post->userid || $_SESSION['userid'] == $comment->userid)
				{
				 
					$data['id'] = $data[0];
					$data['postid'] = $data[1];

					if ($this->commentModel->DeleteComment($data))
					{
						redirect('posts/comment/' . $data[1]);
					}
					else 
					{
						redirect('posts');
					}
				}
				else {
					redirect('posts/comment/' . $data[1]);
				}
			}

		public function delete($id){
			if(!isLoggedIn()){
				redirect('users/login');
			}
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				// Get existing post from model
				$post = $this->postModel->getPostById($id);
				// Check for owner
				
				if($post->userid != $_SESSION['userid']){
					redirect('posts');
				}

				if($this->postModel->deletePost($id)){
					UpdateImageFolder($this->postModel->getPostIds());
					flash('post_message', 'Post Removed');
					redirect('posts');
				} else {
					die('Something went wrong');
				}
			} else {
				redirect('posts');
			}
		}

		public function like($id) {
			if(!isLoggedIn()){
				redirect('users/login');
			}
			$data['userid'] = $_SESSION['userid'];
			$data['postid'] = $id;

			// Get the poster by id to send notification if set
			$post = $this->postModel->getPostById($id);
			$user = $this->userModel->getUserById($post->userid);
			
			// Toggle Post Like
			if ($user->notifications == true)
			{
				$email_data['username'] = $user->uname;
				$email_data['action'] = "Like";
				$email_data['post_title'] = $post->title;
				$email_data['post_url'] = URLROOT . '/posts/index/';

				if (send_mail($user->email, $email_data))
					echo 'EMAIL SENT!';
				else 
					die ('Could not Send EMAIL!');
			}
			if ($likes = $this->likeModel->toggleLike($data))
				redirect('posts/index');
			else 
				die("Something went wrong!");
			// $this->view('posts/index', $data);
		}
	}