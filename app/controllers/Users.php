<?php
	class Users extends Controller {
		public function __construct(){
			$this->userModel = $this->model('User');
			$this->postModel = $this->model('Post');
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
					'notifications' =>  (
						isset($_POST['notification']) && 
						(trim($_POST['notification']) == 'true')
					) ? 1 : 0,
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
					if (!send_mail($data['email'], $email_data)) 
						setFlash('ERROR', 'We could not register you, the email could not be sent!');
						redirect('users/register');
					
						// Register User
					$data['token'] = sha1($token);
					if($this->userModel->register($data)){
						setFlash('SUCCESS', 'You are registered please verify your email to login!');
						redirect('users/login');
					} else {
						setFlash('ERROR', 'We could not register you, something went wrong!');
						redirect('users/register');
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
					'notifications' => true,
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
					setFlash('ERROR', 'Please Verify your email to log in!');
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
				$this->view('users/login', $data);
			}
		}

		public function logout(){
			unset($_SESSION['userid']);
			unset($_SESSION['user_email']);
			unset($_SESSION['user_uname']);
			unset($_SESSION['notifications']);
			session_destroy();
			setFlash('SUCCESS', 'Logged Out Successfully!');
			redirect('users/login');
		}

		public function delete() {
			// Login redirect
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(!isLoggedIn()){
					setFlash('ERROR', 'Please Login to Delete a User!');
					redirect('users/login');
				}
				// Delete logged in user 
				if($this->userModel->deleteUser($_SESSION['userid'])){
					UpdateImageFolder($this->postModel->getPostIds());
					flash('SUCCESS', 'Account Removed successfully!');
					self::logout();
				} else {
					setFlash('ERROR', 'Something went wrong deleting your account!');
					self::logout();
				}
			} else {
				// If get just redirect
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
						if (!send_mail($data['email'], $email_data))
							setFlash('ERROR', 'We could not send a password reset email!');
						redirect('');
					}
					else 
					{
						setFlash('ERROR', 'We could not send a password reset email!');
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
							$data['userid'] = $user->id;
							if ($this->userModel->updateUserPassword($data))
							{
								if ($this->userModel->deletePasswordResetToken($user->id))
								{
									setFlash('SUCCESS', 'Your password has been updated!');
									redirect('');
								}
								else 
								{
									setFlash('ERROR', 'We could not remove your password Token!');
									redirect('');
								}
							}
							else {
								setFlash('ERROR', 'We could not update your password!');
								redirect('');
							}
						}
						else {
							$this->view('users/password', $data);
						}
						
					}
					else 
					{
						setFlash('ERROR', 'Invalid Token Provided!');
						redirect('');
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
						setFlash('ERROR', 'Invalid Token Provided!');
						redirect('');
					}
				}
				else {
					setFlash('SUCCESS', 'Your password has been updated!');
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
						$data['userid'] = $_SESSION['userid'];
						if ($this->userModel->updateUserPassword($data))
						{
							setFlash('SUCCESS', 'Your password has been updated!');
							redirect('');
						}
						else {
							setFlash('ERROR', 'We could not update your password!');
							redirect('');
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
				setFlash('ERROR', 'Please login to update your profile!');
				redirect('users/login');
			}

			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
				$data = [
					'uname' => trim($_POST['uname']),
					'email' => trim($_POST['email']),
					'notifications' =>  (
						isset($_POST['notification']) && 
						(trim($_POST['notification']) == 'true')
					) ? 1 : 0,
					'uname_err' => '',
					'email_err' => ''
				];
				$data = validate_email($data, $this->userModel);
				$data = validate_username($data, $this->userModel);
				
				// Make sure errors are empty
				if(empty($data['email_err']) && empty($data['uname_err'])){
					if($data = $this->userModel->updateUser($data)){
						// $this->logout();
						$this->createUserSession($data);
						// $this->login($data);
						setFlash('SUCCESS', 'Your Profile has been updated!');
						// $this->createUserSession($data);
					} else {
						setFlash('ERROR', 'We could not update this user!');
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
						'notifications' => $_SESSION['notifications'],
						'uname_err' => '',
						'email_err' => '',
				];

				$this->view('users/update', $data);
			}
		}

		public function verify($token = NULL) {
			if (!isset($token))
			{
				setFlash("ERROR", "Invalid token provided for verifying account!");
				redirect('');
			}
			else {
				// Get user by $token and remove his password_hash :) 
				$data = $this->userModel->deleteEmailVerifyHash(sha1($token));
				if ($data == true)
				{
					setFlash("SUCCESS", "Your account has been verified and you can now log in!");
					redirect('users/login');
				}
				else {
					setFlash("ERROR", "Invalid token provided for verifying account!");
					redirect('');
				}
			}
		}

		public function createUserSession($user){
			$_SESSION['userid'] = $user->id;
			$_SESSION['user_email'] = $user->email;
			$_SESSION['user_uname'] = $user->uname;
			$_SESSION['notifications'] = $user->notifications;
			redirect('posts');
		}

	}