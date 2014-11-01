<?php

class MyPDOWrapperForSqlite3 {
	private $sqlite;
	public function __construct($connection) {
		$filename = substr(strstr($connection, ":"), 1);
		$this->sqlite = new SQLite3($filename);
	}
	
	public function prepare($query) {
		return new MyPDOStatementWrapperForSqlite3($query, $this->sqlite->prepare($query));
	}
}

class MyPDOStatementWrapperForSqlite3 {
	private $statement;
	private $result;
	private $query;
	public function __construct($q, $stmt) {
		$this->statement = $stmt;
		$this->query = $q;
	}
	
	public function bindValue($parameter, $value) {
		return $this->statement->bindValue($parameter, $value);
	}
	
	public function execute($parameters = array()) {
		foreach ($parameters as $key => $value) {
			$this->statement->bindValue($key, $value);
		}
		$this->result = $this->statement->execute();
		return $this->result !== FALSE;
	}
	
	public function fetch($fetch_style = PDO::FETCH_BOTH) {
		switch ($fetch_style) {
			case PDO::FETCH_OBJ:
				$res = $this->result->fetchArray(SQLITE3_ASSOC);
				if ($res === FALSE) {
					return FALSE;
				}
				return make_object($res);
			default:
				return $this->result->fetchArray(SQLITE3_BOTH);				
		}
	}
}
