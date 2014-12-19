<?php 
	class DbConnect{
		private $conn;

		//constructor
		function __construct(){
			//connecting to database
			$this->connect();
		}

		//destructor
		function __destruct(){
			//cloding db connection
			$this->close();
		}

		/**
     	* Establishing database connection
     	* @return database handler
     	*/
     	function connect(){
     		include_once dirname(__FILE__) . '/config.php';

     		//connecting to mysql database
     		$this->conn = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD) or die(mysql_error());

     		//selecting database
     		mysql_select_db(DB_NAME) or die (mysql_error());

     		//returning connection resource
     		return $this->conn;
     	}

     	/**
     	*cloding database connection
     	*/

     	function close(){
     		//closing db connection
     		mysql_close($this->conn);
     	}

	}
 ?>