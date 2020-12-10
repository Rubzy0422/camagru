<?php
	class Image {
		private $db;

		public function __construct(){
			$this->db = new Database;
		}

		// 1. Add image
		public function addImage($data){
			$this->db->query('INSERT INTO images (postid, image_path, sticker_path, userimage_path) VALUES(:postid, :image_path, :sticker_path, :userimage_path)');
			$this->db->bind(':postid', $data['postid']);
			$this->db->bind('image_path', $data['image_path']);
			$this->db->bind('sticker_path', $data['sticker_path']);
			$this->db->bind('userimage_path', $data['userimage_path']);
			
			if($this->db->execute()){
				$this->db->query('SELECT LAST_INSERT_ID() as LID;');
				$row = $this->db->single();
				$id = $row->LID;
				return $id;
			}
			else {
				return false;
			}
		}
	}