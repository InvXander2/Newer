<?php

class Database {
 
	private $server = "mysql:host=sql201.infinityfree.com;dbname=if0_39045086_hyip_db";
	private $username = "if0_39045086";
	private $password = "Xgyuc8McZpz8Rr";
	private $options  = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	);
	protected $conn;
 	
	public function open(){
 		try {
 			$this->conn = new PDO($this->server, $this->username, $this->password, $this->options);
 			return $this->conn;
 		} catch (PDOException $e) {
 			echo "There is some problem in connection: " . $e->getMessage();
 		}
    }
 
	public function close(){
   		$this->conn = null;
 	}
}

$pdo = new Database();
?>
