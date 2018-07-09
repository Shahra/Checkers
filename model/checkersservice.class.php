<?php
session_start();

class CheckersService {

	public static function getOnlinePlayers() {
		$onlinePlayers = [];

		try {
			$db = DB::getConnection();
			$st = $db->prepare("SELECT username
													FROM   users
													WHERE  online LIKE '1'
																 AND username NOT LIKE :username");
			$st->execute(array('username' => $_SESSION['username']));
		}

		catch(PDOException $e) {
			exit('PDO error ' . $e->getMessage());
		}

		while($row = $st->fetch()) {
			$onlinePlayers[] = $row['username'];
		}

		return $onlinePlayers;
	}
};

?>

