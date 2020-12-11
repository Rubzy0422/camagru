<?php
	// Load Image Model to use
	require_once  APPROOT . '/models/Image.php';

	class Post {
		private $db;

		public function __construct(){
			$this->db = new Database;
			$this->imageModel = new Image;
		}

		public function getPosts(){
			$this->db->query('SELECT posts.id as postId,
			posts.created_at as postCreated,
			posts.title,
			posts.body,
			users.id as userid,
			users.created_at as userCreated,
			users.uname,
			users.email,
			users.notifications, 
			images.userimage_path ,
            count(likes.id) as likes FROM `posts`
            
            LEFT JOIN users ON users.id = posts.userid
			LEFT JOIN likes ON likes.postid = posts.id
            LEFT JOIN images ON posts.imageid = images.id
			GROUP BY posts.id
			ORDER BY posts.created_at DESC');
			$results = $this->db->resultSet();

			return $results;
		}

		public function getPostIds() {
			$this->db->query('SELECT posts.id FROM `posts` ORDER BY posts.created_at DESC');
			$results = $this->db->resultSet();

			return $results;
		}

		public function addPost($data){
			$this->db->query('INSERT INTO posts (title, userid, body) VALUES(:title, :userid, :body)');
			$this->db->bind(':title', $data['title']);
			$this->db->bind(':userid', $data['userid']);
			$this->db->bind(':body', $data['body']);

			// get that Id :) 
			if($this->db->execute()){
				//return true;
					// now add the image paths
					$this->db->query('SELECT LAST_INSERT_ID() as LID;');
					$row = $this->db->single();
					$id = $row->LID;
					
					// Well now that we've added the post we need to add it's images aswell 
					$img_dst = APPROOT. '/Images/combo/' . $id;
					$img_src = APPROOT. '/Images/userimages/' . $id;
					$img_stick = APPROOT. '/Images/stickers/' . $id;
					$ext = '.png';
					
					createImage($data['userimg'], $img_src);
					createImage($data['stickerimg'], $img_stick);
					mergeImages( $img_src . $ext , $img_stick . $ext, $img_dst . $ext); 
					
					$data = [
						'postid' => $id,
						'image_path' => $img_src . $ext,
						'sticker_path' => $img_stick  . $ext,
						'userimage_path' => $img_dst . $ext,
					];
					// Add image model 
					if (($imageid = $this->imageModel->addImage($data)))
					{
						// Ok so here we ad the image id field to the post :) 
						$this->db->query('UPDATE posts SET imageid = :imageid WHERE id=:id');
						$this->db->bind(':imageid', $imageid);
						$this->db->bind(':id', $id);						
						
						if($this->db->execute()){
							return $id;
						} else {
							return false;
						}
					}
					else {
						return false;
					}
					
			} else {
				return false;
			}


			// Execute
		}

		public function updatePost($data){
			$this->db->query('UPDATE posts SET title = :title, body = :body WHERE id = :id');
			// Bind values
			$this->db->bind(':id', $data['id']);
			$this->db->bind(':title', $data['title']);
			$this->db->bind(':body', $data['body']);

			// Execute
			if($this->db->execute()){
				return true;
			} else {
				return false;
			}
		}

		public function getPostById($id){
			$this->db->query('SELECT posts.id as postId,
									 posts.title,
									posts.body,
									posts.userid,
									images.userimage_path,
									count(likes.id) as likes FROM `posts`
									INNER JOIN images ON posts.imageid = images.id
									LEFT JOIN likes ON likes.postid = posts.id
									WHERE posts.id = :id;');
			$this->db->bind(':id', $id);
			$row = $this->db->single();
			return $row;
		}

		public function deletePost($id){
			$this->db->query('DELETE FROM posts WHERE id = :id');
			// Bind values
			$this->db->bind(':id', $id);

			// Execute
			if($this->db->execute()){
				return true;
			} else {
				return false;
			}
		}
	}