<?php
	class User {
		private $db;

		public function __construct(){
			$this->db = new Database;
		}

		// Regsiter user
		public function register($data){
			$this->db->query('INSERT INTO users (uname, email, password) VALUES(:uname, :email, :password)');
			// Bind values
			$this->db->bind(':uname', $data['uname']);
			$this->db->bind(':email', $data['email']);
			$this->db->bind(':password', $data['password']);

			// Execute
			if($this->db->execute()){
				return true;
			} else {
				return false;
			}
		}

		// Login User
		public function login($uname, $password){
			$this->db->query('SELECT * FROM users WHERE uname = :uname');
			$this->db->bind(':uname', $uname);

			$row = $this->db->single();

			$hashed_password = $row->password;
			if(password_verify($password, $hashed_password)){
				return $row;
			} else {
				return false;
			}
		}

		// Find user by email
		public function findUserByEmail($email){
			$this->db->query('SELECT * FROM users WHERE email = :email');
			$this->db->bind(':email', $email);
			$row = $this->db->single();
			if($this->db->rowCount() > 0){
				return true;
			} else {
				return false;
			}
		}
		// Find user by Username
		public function findUserByUsername($uname){
			$this->db->query('SELECT * FROM users WHERE uname = :uname');
			$this->db->bind(':uname', $uname);
			$row = $this->db->single();
			if($this->db->rowCount() > 0){
				return true;
			} else {
				return false;
			}
		}
		
		// Get User by ID
		public function getUserById($id){
			$this->db->query('SELECT * FROM users WHERE id = :id');
			$this->db->bind(':id', $id);
			$row = $this->db->single();
			return $row;
		}
	}