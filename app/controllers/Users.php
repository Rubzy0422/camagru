<?php
	class Users extends Controller {
		public function __construct(){
			$this->userModel = $this->model('User');
		}

		public function register(){
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				// Sanitize POST data
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
				$data =[
					'uname' => trim($_POST['uname']),
					'email' => trim($_POST['email']),
					'password' => trim($_POST['password']),
					'confirm_password' => trim($_POST['confirm_password']),
					'uname_err' => '',
					'email_err' => '',
					'password_err' => '',
					'confirm_password_err' => ''
				];
				$data = validate_email($data, $this->userModel);
				$data = validate_username($data, $this->userModel);
				$data = validate_passwords($data);
				// Make sure errors are empty
				if(empty($data['email_err']) && empty($data['uname_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])){
					// Hash Password
					$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
					// Register User
					if($this->userModel->register($data)){
						flash('register_success', 'You are registered and can log in');
						redirect('users/login');
					} else {
						die('Something went wrong');
					}
				} else {
					// Load view with errors if any
					$this->view('users/register', $data);
				}
			} else {
				// Init data and view
				$data =[
					'uname' => '',
					'email' => '',
					'password' => '',
					'confirm_password' => '',
					'uname_err' => '',
					'email_err' => '',
					'password_err' => '',
					'confirm_password_err' => ''
				];
				$this->view('users/register', $data);
			}
		}

		public function login(){
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
				// Init data
				$data =[
					'uname' => trim($_POST['uname']),
					'password' => trim($_POST['password']),
					'uname_err' => '',
					'password_err' => '',			
				];
				
				if(empty($data['uname']) || empty($data['password'])){
					$data['uname_err'] = 'Please enter your Credentials';
				}
				// Check for user using username
				if(!$this->userModel->findUserByUsername($data['uname'])){
					$data['uname_err'] = 'Invalid Login Credentials';
					$data['password_err'] = 'Invalid Login Credentials';
				}
				
				if(empty($data['uname_err']) && empty($data['password_err'])){
					$loggedInUser = $this->userModel->login($data['uname'], $data['password']);
					if($loggedInUser){
						$this->createUserSession($loggedInUser);
					} else {
						$data['uname_err'] = 'Invalid Login Credentials';
						$data['password_err'] = 'Invalid Login Credentials';
						$this->view('users/login', $data);
					}
				} else {
					$this->view('users/login', $data);
				}
			} else {
				$data =[		
					'uname' => '',
					'password' => '',
					'uname_err' => '',
					'password_err' => ''
				];

				// Load view
				$this->view('users/login', $data);
			}
		}

		public function createUserSession($user){
			$_SESSION['user_id'] = $user->id;
			$_SESSION['user_email'] = $user->email;
			$_SESSION['user_uname'] = $user->uname;
			redirect('posts');
		}

		public function logout(){
			unset($_SESSION['user_id']);
			unset($_SESSION['user_email']);
			unset($_SESSION['user_uname']);
			session_destroy();
			redirect('users/login');
		}
	}