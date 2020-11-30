<?php 
	// Get The Logged in user :) and compare against that 
	function validate_email($data, $userModel)
	{
		// Validate Email
		if(empty($data['email'])){
			$data['email_err'] = 'Please enter your email';
		} else {
			// Check email
			if($userModel->findUserByEmail($data['email'])){
				if(isLoggedIn() && $data['email'] == $_SESSION['user_email'])
				{
					return $data;
				}
				$data['email_err'] = 'Email is already taken';
			}
		}
		return $data;
	}

	function validate_username($data, $userModel)
	{
		// Validate username
		if(empty($data['uname'])){
			$data['uname_err'] = 'Please enter your Username';
		} else {
			// Check Username
			if($userModel->findUserByUsername($data['uname'])){
				if(isLoggedIn() && $data['uname'] == $_SESSION['user_uname'])
				{
					return $data;
				}
				$data['uname_err'] = 'Username is already taken';
			}
		}
		return $data;
	}

	function validate_passwords($data)
	{
		// Validate Confirm Password
		if(empty($data['confirm_password'])){
			$data['confirm_password_err'] = 'Pleae confirm password';
		} else {
			if($data['password'] != $data['confirm_password']){
				$data['confirm_password_err'] = 'Passwords do not match';
			}
		}

		// Validate Password
		if(empty($data['password'])){
			$data['password_err'] = 'Please enter password';
		}
		else {
			if(strlen($data['password'] ) > 255) {
				$data['password_err'] = "Password is too long!";
			}
			if(strlen($data['password'] ) < 8)
			{
				$data['password_err'] = "Password must contain atleast 8 characters!";
			}
			if(!preg_match("#[0-9]+#", $data['password']))
			{
				$data['password_err'] = "Password must include at least one number!";
			}
			if(!preg_match("#[a-z]+#", $data['password']))
			{
				$data['password_err'] = "Password must include at least one letter!";
			}	
			if(!preg_match("#[A-Z]+#", $data['password']))
			{
				$data['password_err'] = "Password must include at least one CAPS!";
			}	
			if(!preg_match("#\W+#", $data['password']))
			{
				$data['password_err'] = "Password must include at least one symbol!";
			}
		}
		return $data;
	}
?>