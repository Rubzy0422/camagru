<?php 
	/*
	* Send A Email: Emails contain the following catogories: 
	* 1. Like on post
	* 2. Comment on post
	* 3. Password reset
	* 4. Email Verification

	* Templates are stored under views/emails/...
	
	// Data Requirements : 
	 Like / Comment Requires :
	 1. username
	 2. post_title
	 3. action 
	 4. post_url

	 Register / PassReset Requires : 
	 1. username 
	 2. verif_url
	 */
	function send_mail($email, $data)
	{
		// Get Action 
		if ($data['action'] == "Like" || $data['action'] == "Comment")
		{
			$content = Template::get_contents("../app/views/emails/post_action.html", $data);
		}
		else if ($data['action'] == "Password Reset" || $data['action'] == "Register")
		{
			$content = Template::get_contents("../app/views/emails/pass_verif.html", $data);
		}
		// Set Email Subject
		$subject = "Camagru - " . $data['action']; 
		//  Set Email Headers
		$headers	=	'MIME-Version: 1.0'."\r\n";
		$headers	.=	'Content-type: text/html;charset=utf-8'."\r\n";
		$headers	.=	'To: '.$email."\r\n";
		$headers	.=	'From: Camagru WebApp <noreply@camagru.co.za>'."\r\n";
		return mail($email, $subject, $content, $headers);
	}
	?>