<?php

class DB {
	private static $db = null;

	private function __construct() {}
	private function __clone() {}

	public static function getConnection() {
		if(DB::$db === null) {
			try {
				DB::$db = new PDO("mysql: host=localhost; dbname=checkers; charset=utf8", 'root', '');
				DB::$db-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch(PDOException $e) {
				exit('PDO Error: ' . $e->getMessage());
			}
		}
		return DB::$db;
	}
}

?>
