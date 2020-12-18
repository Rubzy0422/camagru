<?php
	class Comment {
		private $db;

		public function __construct(){
			$this->db = new Database;
		}

		public function getCommentForId($id) {
			// So now we know hehehe
			$this->db->query('SELECT comments.comment, comments.id, comments.postid, users.uname FROM comments INNER JOIN users ON users.id = comments.userid  WHERE postid = :postid ORDER BY comments.id DESC');
			$this->db->bind(':postid', $id);
			$results = $this->db->resultSet();
			return $results;
		}

		public function addComment($data) {
			$this->db->query('INSERT INTO `comments`( `userid`, `postid`, `comment`) VALUES (:userid, :postid, :comment)');
			$this->db->bind(':userid', $data['userid']);
			$this->db->bind(':postid', $data['postid']);
			$this->db->bind(':comment', $data['comment']);

			// get that Id :) 
			if($this->db->execute()){
				return $this->getCommentForId($data['postid']);
			}
			else {
				return false;
			}
		}

		public function getCommentById($id) {
			$this->db->query('SELECT *  FROM `comments` WHERE `comments`.`id` = :id');
			$this->db->bind(':id', $id);
 
			$row = $this->db->single();
			return $row;
		}

		public function DeleteComment($data) {
			$this->db->query('DELETE FROM `comments` WHERE `comments`.`id` = :id');
			$this->db->bind(':id', $data['id']);

			// get that Id :) 
			if($this->db->execute()){
				return $this->getCommentForId($data['postid']);
			}
			else {
				return false;
			}
		}
	}