<?php 
	/*
	* This file makes Camagru Setup for the initial run and should be removed in a production release
	* as it would remove all user data and reset to first run.
	*/
	class Setup {
		private static $host = DB_HOST;
		private static $DB_USER = DB_USER;
		private static $DB_PASSWORD = DB_PASS;

		private static $dbh;
		private static $error;

		public static function InitDB(){
			// Set DSN
			$DB_DNS = 'mysql:host=' . self::$host;
			$options = array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			);

			// Create PDO instance
			try{
				self::$dbh = new PDO($DB_DNS, self::$DB_USER, self::$DB_PASSWORD, $options);
			} catch(PDOException $e){
				self::$error = $e->getMessage();
				die(self::$error);
			}
			try {
				self::$dbh->beginTransaction();
				$query = file_get_contents('camagru.sql', FILE_USE_INCLUDE_PATH);
				$stmt = self::$dbh->prepare($query);
				if ($stmt->execute()) 
				{
					echo "<script>alert('DB_SETUP Success');</script>";
				}
				self::$dbh->commit();
			} 
			catch(Exception $e){
				self::$dbh->rollBack();
				die($e->getMessage());
			}
		}
	}
?>