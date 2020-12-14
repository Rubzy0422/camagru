<?php 
	/* Basic flashes 1. SUCCESS 
					 2. ERROR 
					 3. INFO 
		usage , set the type, and then the message 

					 */
session_start();

	function setFlash($id ,$message){
		$_SESSION["flash_{$id}"] =  $message;
	}

	function getFlashes(){
		$flashes = array();
		foreach($_SESSION as $id => $value){
			if(strpos($id, "flash_") === 0){
			
				if ($id == "flash_SUCCESS")
				{
					$class = "alert alert-success";
				}
				elseif ($id == "flash_ERROR")
				{
					$class = "alert alert-danger";
				}
				elseif ($id == "flash_INFO")
				{
					$class = "alert alert-info";
				}
				echo '<div class="' . $class . '" id="msg-flash">' . $value .'</div>';
				unset($_SESSION[$id]);
			}
		}
}

function isLoggedIn(){
	if(isset($_SESSION['userid'])){
		return true;
	} else {
		return false;
	}
}