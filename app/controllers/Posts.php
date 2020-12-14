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

			$count = $this->postModel->getPostCount();
			echo '<br />';
			$data = $this->postModel->getPosts((int)$page);
			$data['maxpage'] = ceil($count->postcount / $this->postModel->rpp);
			
			$this->view('posts/index', $data);
		}

		public function add(){
			if(!isLoggedIn()){
				setFlash('ERROR', 'Please Login to add a post!');
				redirect('users/login');
			}
			$stickers = glob('../public/stickers/*.{jpg,png,gif}', GLOB_BRACE);
			
			$prevPosts = $this->postModel->getUserPosts();
			foreach ($prevPosts as $post)
			{
				$path = $post->userimage_path;
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$imgdata = file_get_contents($path);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($imgdata);
				$post->imgsrc = $base64; 
			}

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
						setFlash('SUCCESS', 'Post Added!');
						redirect('posts');
					} else {
						//Image not created or what ever lol
						setFlash('ERROR', 'We could not add that post!');
						redirect('posts');
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
				setFlash('ERROR', 'Please Login to edit a post!');
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
						setFlash('SUCCESS', 'Post Updated successfully!');
						redirect('posts');
					} else {
						setFlash('ERROR', 'Post could not be updated, something went wrong!');
						redirect('posts');
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
					setFlash('ERROR', 'You are not allowed to edit this post!');
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
			if(!isLoggedIn()){
				setFlash('ERROR', 'Please login to make a comment!');
				redirect('users/login');
			}
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
							setFlash('ERROR', 'We could not send the email to the poster!');
					}

					if ($updatedComment = $this->commentModel->addComment($userdata))
					{
						$data['comments'] = $updatedComment;
					}
					else {
						setFlash('ERROR', 'We could not add that comment, something went wrong!');
						redirect('');
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
			// Comment Id, Post Id makes a id to delete by :)
				$data = explode('|', $id);
				if (count($data) != 2)
				{
					setFlash('ERROR', 'We could not delete this Comment, you provided a Invalid id!');
					redirect('posts');
				}
				// if post does not exist 
				$comment = $this->commentModel->getCommentById($data[0]);
				$post = $this->postModel->getPostById($data[1]);

				if ($post->postId == NULL)
				{
					setFlash('ERROR', 'We could not delete this Comment, you provided a Invalid id!');
					redirect('posts');
				}

				if ($comment == false)
				{
					setFlash('ERROR', 'We could not delete this Comment, you provided a Invalid id!');
					redirect('posts/comment/' . $data[1]);
				}
				// Creator of post or creator of comment
				if ($_SESSION['userid'] == $post->userid || $_SESSION['userid'] == $comment->userid)
				{
					$data['id'] = $data[0];
					$data['postid'] = $data[1];

					$res = $this->commentModel->DeleteComment($data);
					if ($res || $res == [])
					{
						setFlash('SUCCESS', 'Comment Removed Succesfully!');
						redirect('posts/comment/' . $data[1]);
					}
					else 
					{
						setFlash('ERROR', 'We could not delete your comment something went wrong!');
						redirect('posts');
					}
				}
				else {
					setFlash('ERROR', 'You are not allowed to remove this comment!');
					redirect('posts/comment/' . $data[1]);
				}
			}

		public function delete($id){
			if(!isLoggedIn()){
				setFlash('Error', 'Please Login to delete a post!');
				redirect('users/login');
			}
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				// Get existing post from model
				$post = $this->postModel->getPostById($id);
				// Check for owner
				
				if($post->userid != $_SESSION['userid']){
					setFlash('ERROR', 'You are not allowed to remove this post!');
					redirect('posts');
				}

				if($this->postModel->deletePost($id)){
					UpdateImageFolder($this->postModel->getPostIds());
					setFlash('SUCCESS', 'Post Removed Succesfully!');
					redirect('posts');
				} else {
					setFlash('ERROR', 'we could not remove this post, something went wrong!');
					redirect('');
				}
			} else {
				// You gave a get request just redirect
				redirect('posts');
			}
		}

		public function like($id) {
			if(!isLoggedIn()){
				setFlash('ERROR', 'Please Login to like a post!');
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
			}
			if ($likes = $this->likeModel->toggleLike($data))
			{
				if ($likes[1] === true)
				{
					if (!send_mail($user->email, $email_data))
						setFlash('ERROR', 'We could not send a email to the poster!');
						
					setFlash('SUCCESS', 'Like Added!');
					redirect('posts/index');
				}
				elseif ($likes[1] === false) 
				{
					setFlash('SUCCESS', 'Like Removed!');
					redirect('posts/index');
				}
			}
			else 
				setFlash('ERROR', 'We could not add or remove your like!');
				redirect('');
				// $this->view('posts/index', $data);
		}
	}