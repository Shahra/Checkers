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

	public static function insideGame() {
		try {
			$db = DB::getConnection();
			$st = $db->prepare("SELECT status
													FROM   games
													WHERE  (black_name = :username OR white_name = :username)
			 													 AND status IN ('WHITE_TO_MOVE', 'BLACK_TO_MOVE');");
			$st->execute(array('username' => $_SESSION['username']));
		}

		catch(PDOException $e) {
			exit('PDO error ' . $e->getMessage());
		}

		$row = $st->fetch();

		if($row === false) { return false; }

		return true;
	}

	public static function challengePlayer($name) {
		try {
			$db = DB::getConnection();
			$st = $db->prepare("INSERT INTO games( 
																			white_name, 
																			black_name) 
													VALUES      (:white_name, 
																			 :black_name);");
			$st->execute(array('white_name' => $_SESSION['username'],
												 'black_name' => $name
			));
		}

		catch(PDOException $e) {
			exit('PDO error ' . $e->getMessage());
		}
	}

	public static function checkIfChallenged() {
		try {
			$db = DB::getConnection();
			$st = $db->prepare("SELECT white_name
													FROM   games
													WHERE  black_name = :username
															   AND status = 'PENDING_REQUEST';");
			$st->execute(array('username' => $_SESSION['username']));
		}

		catch(PDOException $e) {
			exit('PDO error ' . $e->getMessage());
		}

		$row = $st->fetch();

		if($row === false) { return false; }

		return $row['white_name'];
	}

	public static function acceptChallenge($name) {
		try {
			$db = DB::getConnection();
			$st = $db->prepare("UPDATE games
													SET    status = 'WHITE_TO_MOVE'
													WHERE  white_name = :white_name
																 AND black_name = :black_name
																 AND status = 'PENDING_REQUEST';");
			$st->execute(array('white_name' => $name,
				'black_name' => $_SESSION['username']));
		}

		catch(PDOException $e) {
			exit('PDO error ' . $e->getMessage());
		}
	}

	public static function declineChallenge($name) {
		try {
			$db = DB::getConnection();
			$st = $db->prepare("DELETE FROM games 
													WHERE  white_name = :white_name 
																 AND black_name = :black_name
																 AND status = 'PENDING_REQUEST';");
			$st->execute(array('white_name' => $name,
												 'black_name' => $_SESSION['username']));
		}

		catch(PDOException $e) {
			exit('PDO error ' . $e->getMessage());
		}
	}

};

?>

