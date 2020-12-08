<?php
	// Load Config
	require_once 'config/config.php';
	// Load Helpers
	require_once 'helpers/url_helper.php';
	require_once 'helpers/session_helper.php';
	require_once 'helpers/sendmail.php';
	
	// Load Validators 
	require_once 'helpers/Validators/post_validator.php';
	require_once 'helpers/Validators/user_validator.php';
	// Autoload Core Libraries
	spl_autoload_register(function($className){
		require_once 'libraries/' . $className . '.php';
	});
	
