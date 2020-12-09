<?php
	class User {
		private $db;

		public function __construct(){
			$this->db = new Database;
		}

		// Regsiter user
		public function register($data){
			$this->db->query('INSERT INTO users (uname, email, password, verify_Hash, notifications) VALUES(:uname, :email, :password, :token, :notifications)');
			// Bind values
			$this->db->bind(':uname', $data['uname']);
			$this->db->bind(':email', $data['email']);
			$this->db->bind(':password', $data['password']);
			$this->db->bind(':token', $data['token']);
			$this->db->bind(':notifications', $data['notifications']);

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
				return $row;
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
				return $row;
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

		// Delete User
		public function deleteUser($id){
			$this->db->query('DELETE FROM users WHERE id = :id');
			// Bind values
			$this->db->bind(':id', $id);

			// Execute
			if($this->db->execute()){
				return true;
			} else {
				return false;
			}
		}

		// Update User 
		public function updateUser($data) {
			$this->db->query('UPDATE users SET uname =:uname,  email=:email, notifications=:notifications WHERE id =:id ');
			// Bind values
			$this->db->bind(':uname', $data['uname']);
			$this->db->bind(':email', $data['email']);
			$this->db->bind(':notifications', $data['notifications']);
			$this->db->bind(':id', $_SESSION['user_id']);
			// Execute
			if($this->db->execute()){
				return $this->findUserByUsername($data['uname']);
			} else {
				return false;
			}
		}

		// Update User Pass
		public function updateUserPassword($data) {
			$this->db->query('UPDATE users SET password =:password WHERE id =:id ');
			// Bind values
			$this->db->bind(':password', $data['password']);
			$this->db->bind(':id', $data['user_id']);
			// Execute
			if($this->db->execute()){
				return true;
			} else {
				return false;
			}
		}

		public function setPasswordResetToken($data) {
			$this->db->query('UPDATE users SET reset_Hash =:token WHERE id =:id ');
			// Bind values
			$this->db->bind(':token', $data['token']);
			$this->db->bind(':id', $data['id']);
			// Execute
			if($this->db->execute()){
				return true;
			} else {
				return false;
			}
		}

		public function getPasswordResetTokenUser($token) {
			$this->db->query('SELECT * from users WHERE reset_Hash =:token');
			// Bind values
			$this->db->bind(':token', $token);
			// Execute
			$row = $this->db->single();
			if($this->db->rowCount() > 0){
				return empty($row->reset_Hash) ? false : $row;
			} else {
				return false;
			}
		}

		public function deletePasswordResetToken($id) {
				$this->db->query('UPDATE users SET reset_Hash = NULL WHERE id = :id');
				$this->db->bind(':id', $id);
				if($this->db->execute()){
					return true;
				} else {
					return false;
				}
			}

		public function deleteEmailVerifyHash($token) {
			$this->db->query('UPDATE `users` SET `verify_Hash` = NULL WHERE `verify_Hash` = :token');
				$this->db->bind(':token', $token);
				if($this->db->execute()){
					return true;
				} else {
					return false;
				}
		}
	}