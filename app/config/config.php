<?php
	// DB Params
	define('DB_HOST', 'localhost');
	define('DB_USER', 'root');
	define('DB_PASS', 'password');
	define('DB_NAME', 'camagru');

	// App Root
	define('APPROOT', dirname(dirname(__FILE__)));
	// URL Root
	define('URLROOT', 'http://localhost:8080/camagru');
	// Site Name
	define('SITENAME', 'camagru');
	// App Version
	define('APPVERSION', '1.0.0');
	
	require_once 'setup.php';
	require_once 'Database.php';
