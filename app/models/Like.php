<?php
	class Like {
		private $db;

		public function __construct(){
			$this->db = new Database;
		}

		/*
			this SQL let's a user add or remove his like from the pool of likes per post
			and returns the number of likes a post has.

			in short 
			1. Select the  likes table where userid and postid is that of the current user and post
			2. if the count is >= one then remove users row (that like id) otherwise add it.
			3. return the amount of rows where the postid is current post  

			REQUIRED:
				userid 
				postid
		*/
		
		public function toggleLike($data){
			$didlike = $this->didLike($data);
			if ($didlike->likes == "0")
			{
				if ($this->addLike($data))
					return [$this->getLikes($data['postid']), true];
			}
			else 
			{
				if ($this->removeLike($data))
					return [$this->getLikes($data['postid']), false];
			}
			return false;
		}

		public function getLikes($id) {
			$this->db->query('SELECT COUNT(`id`) as likes FROM `likes` WHERE `postid` =:postid');
			$this->db->bind(':postid', $id);
			return $this->db->single();
		}

		public function addLike($data) {
			$this->db->query('INSERT INTO `likes`(`userid`, `postid`) VALUES (:userid, :postid)');
			$this->db->bind(':postid', $data['postid']);
			$this->db->bind(':userid', $data['userid']);
			
			if($this->db->execute()){
				return true;
			} else {
				return false;
			}
		}

		public function removeLike($data) {
			$this->db->query('DELETE FROM likes WHERE `userid` =:userid AND `postid` =:postid');
			$this->db->bind(':postid', $data['postid']);
			$this->db->bind(':userid', $data['userid']);
				
			if($this->db->execute()){
				return true;
			} else {
				return false;
			}
		}

		public function didLike($data) {
			$this->db->query('SELECT COUNT(`id`) as likes FROM `likes` WHERE `postid` =:postid AND `userid`=:userid');
			$this->db->bind(':postid', $data['postid']);
			$this->db->bind(':userid', $data['userid']);
			return $this->db->single();
		}

	}