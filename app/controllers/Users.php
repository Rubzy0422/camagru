<?php
	class Users extends Controller {
		public function __construct(){
			$this->userModel = $this->model('User');
		}

		public function index() {
			redirect('users/login');
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
					'token' => '',
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
					$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
					// Create The token 
					
					$cstrong = True;
					$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
					
					$email_data['username'] = $data['uname'];
					$email_data['verif_url'] = URLROOT . "/users/verify/" . $token;
					$email_data['action'] = "Register";
					if (send_mail($data['email'], $email_data))
						echo 'EMAIL SENT!';
					else 
						die ('Could not Send EMAIL!');
					// Register User
					$data['token'] = sha1($token);
					if($this->userModel->register($data)){
						flash('register_success', 'You are registered please verify your email to login!');
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
				$user = $this->userModel->findUserByUsername($data['uname']);
				if(!$user) {
					$data['uname_err'] = 'Invalid Login Credentials';
					$data['password_err'] = 'Invalid Login Credentials';
				}
				if (isset($user->verify_Hash))
				{
					flash('error', 'Please Verify your email to log in!');
					$this->view('users/login', $data);
				}
				else 
				{
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

		public function logout(){
			unset($_SESSION['user_id']);
			unset($_SESSION['user_email']);
			unset($_SESSION['user_uname']);
			session_destroy();
			redirect('users/login');
		}

		public function delete() {
			// Login redirect
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(!isLoggedIn()){
					redirect('users/login');
					die (' NOT LOGIN ?');
				}
				// Delete logged in user 
				if($this->userModel->deleteUser($_SESSION['user_id'])){
					flash('user_message', 'User Removed');
					self::logout();
				} else {
					die('Something went wrong');
				}
			} else {
				redirect('');
			}
		}

		public function forgotPass() {
			$data = [
				'email' => '',
				'email_err' => ''
			];

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				// Generate token
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
				$data['email'] = $_POST['email'];
				$cstrong = True;
				$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));

				// Get user_by email 
				if($udata = $this->userModel->findUserByEmail($data['email']))
				{
					$email_data['username'] = $udata->uname;
					$email_data['verif_url'] = URLROOT . "/users/updatePass/" . $token;
					$email_data['action'] = "Password Reset";
					
					// Insert Reset Token in DB
					$dbdat = [
						'id' => $udata->id,
						'token' => sha1($token)
					];
					if ($this->userModel->setPasswordResetToken($dbdat))
					{
						if (send_mail($data['email'], $email_data))
							echo 'EMAIL SENT!';
						else 
							die ('Could not Send EMAIL!');
						redirect('');
					}
					else 
					{
						die("Unexpected error while setting reset Token!");
					}
				}
				
			}
			$this->view('users/forgotPass', $data);

		}

		public function updatePass($token = NULL) {
			// Login redirect
			if(!isLoggedIn()){
				// User Not Logged in Display 
				// Validate Token :) 
				$data = [
					'old_password' => '',
					'password' => '',
					'confirm_password' => '',
					'token' => $token,

					'old_password_err' => '',
					'password_err' => '',
					'password_confirm_err' => '',
					'token_err' => '',
					'display' => false
				];

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					
					$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
					if ($user = $this->userModel->getPasswordResetTokenUser(sha1($token)))
					{
						$data['password'] = isset($_POST['password']) ? $_POST['password'] : '';
						$data['confirm_password'] = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
						
						// Validate Password Error ? 

						$data = validate_passwords($data);
						if (empty($data['password_err'] && empty($data['confirm_password_err'])))
						{
							$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
							$data['user_id'] = $user->id;
							if ($this->userModel->updateUserPassword($data))
							{
								if ($this->userModel->deletePasswordResetToken($user->id))
								{
									redirect('');
								}
								else 
								{
									die ("Could not Delete Password Token");
								}
							}
							else {
								die("Something went wrong!");
							}
						}
						else {
							$this->view('users/password', $data);
						}
						
					}
					else 
					{
						die('Invalid Token!');
					}
					// $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
				}
				if (isset($token))
				{
					if ($user = $this->userModel->getPasswordResetTokenUser(sha1($token)))
					{
						$data = [
							'old_password' => '',
							'password' => '',
							'confirm_password' => '',
							'token' => $token,

							'old_password_err' => '',
							'password_err' => '',
							'password_confirm_err' => '',
							'token_err' => '',
							'display' => false
						];


						$this->view('users/password', $data);
					}
					else 
					{
						die('Invalid Token!');
					}

				}
				else {
					redirect('users/login');
				}
			}
			else {
				// Logged in User Display
				$data = [
					'old_password' => '',
					'password' => '',
					'confirm_password' => '',
					
					'old_password_err' => '',
					'password_err' => '',
					'password_confirm_err' => '',
					'display' => true
				];

				if($_SERVER['REQUEST_METHOD'] == 'POST'){
					$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
					// Current Password Verification :)
					$udata = $this->userModel->findUserByUsername($_SESSION['user_uname']);
					$data['password'] = isset($_POST['password']) ? $_POST['password'] : '';
					$data['confirm_password'] = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
					if (password_verify($_POST['old_password'], $udata->password))
					{
						$data = validate_passwords($data);
						// Update Password now 
						$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
						$data['user_id'] = $_SESSION['user_id'];
						if ($this->userModel->updateUserPassword($data))
						{
							redirect('');
						}
						else {
							die("Something went wrong!");
						}
					}
					else {
						$data['old_password_err'] = "Incorrect old Password!";
					}
				}
				
				$this->view('users/password', $data);
			}
		}

		public function update() {
			// Login redirect
			if(!isLoggedIn()){
				redirect('users/login');
			}

			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
				$data = [
					'uname' => trim($_POST['uname']),
					'email' => trim($_POST['email']),
					'uname_err' => '',
					'email_err' => ''
				];
				$data = validate_email($data, $this->userModel);
				$data = validate_username($data, $this->userModel);
				
				// Make sure errors are empty
				if(empty($data['email_err']) && empty($data['uname_err'])){
					if($data = $this->userModel->updateUser($data)){
						flash('update_success', 'Your Profile has been updated!');
						self::logout();
						
					} else {
						die('Something went wrong');
					}
				} else {
					// Load view with errors if any
					$this->view('users/update', $data);
				}
			}
			else {
				$data = [
						'uname' => $_SESSION['user_uname'],
						'email' => $_SESSION['user_email'],
						'uname_err' => '',
						'email_err' => '',
				];

				$this->view('users/update', $data);
			}
		}

		public function verify($token = NULL) {
			if (!isset($token))
			{
				die ('Invalid TOKEN!');
			}
			else {
				// Get user by $token and remove his password_hash :) 
				$data = $this->userModel->deleteEmailVerifyHash(sha1($token));
				if ($data == true)
				{
					redirect('users/login');
				}
				else {
					die ('Invalid TOKEN!');
				}
			}
		}

		public function createUserSession($user){
			$_SESSION['user_id'] = $user->id;
			$_SESSION['user_email'] = $user->email;
			$_SESSION['user_uname'] = $user->uname;
			redirect('posts');
		}

	}