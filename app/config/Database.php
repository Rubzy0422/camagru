<?php
	/*
	 * PDO Database Class
	 * Connect to database
	 * Create prepared statements
	 * Bind values
	 * Return rows and results
	 */
	class Database {
		private $host = DB_HOST;
		private $DB_USER = DB_USER;
		private $DB_PASSWORD = DB_PASS;
		private $dbname = DB_NAME;

		private $dbh;
		private $stmt;
		private $error;

		public function __construct(){
			// Set DSN
			$DB_DNS = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
			$options = array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			);

			// Create PDO instance
			try{
				$this->dbh = new PDO($DB_DNS, $this->DB_USER, $this->DB_PASSWORD, $options);
			} catch(PDOException $e){
				$this->error = $e->getMessage();
				Setup::InitDB();
				die($this->error);
			} 
			
		}
	
		// Prepare statement with query
		public function query($sql){
			$this->stmt = $this->dbh->prepare($sql);
		}

		// Bind values
		public function bind($param, $value, $type = null){
			if(is_null($type)){
				switch(true){
					case is_int($value):
						$type = PDO::PARAM_INT;
						break;
					case is_bool($value):
						$type = PDO::PARAM_BOOL;
						break;
					case is_null($value):
						$type = PDO::PARAM_NULL;
						break;
					default:
						$type = PDO::PARAM_STR;
				}
			}

			$this->stmt->bindValue($param, $value, $type);
		}

		// Execute the prepared statement
		public function execute(){
			// endtransaction
			return $this->stmt->execute(); //bool
			// dbh->rollback();
		}

		// Get result set as array of objects
		public function resultSet(){
			$this->execute();
			return $this->stmt->fetchAll(PDO::FETCH_OBJ);
		}

		// Get single record as object
		public function single(){
			$this->execute();
			return $this->stmt->fetch(PDO::FETCH_OBJ);
		}

		// Get row count
		public function rowCount(){
			return $this->stmt->rowCount();
		}
	}