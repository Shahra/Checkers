<?php
session_start();

class CheckersService {

	public static function getColour($c) {
		if ($c === 'A' || $c === 'B') return "white";
		else if ($c === 'C' || $c === 'D') return "black";
	}

	public static function boardToString($board) {
		$str = "";
		for($i = 0; $i < 8; $i++) {
			for($j = 0; $j < 8; $j++) {
				$str .= $board[$i][$j];
			}
			if($i !== 7) {
				$str .= ";";
			}
		}
		return $str;
	}

	public static function validCoordinate($coord) {
		if($coord < 0 || $coord > 7) { return false; }
		else { return true; }
	}

	public static function getOnlinePlayers() {
		$onlinePlayers = [];

		try {
			$db = DB::getConnection();
			$st = $db->prepare("SELECT username
													FROM   users
													WHERE  online LIKE '1'
																 AND username NOT IN (SELECT white_name FROM games) 
																 AND username NOT in (select  black_name from games)
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
			 													 AND status IN ('WHITE', 'BLACK');");
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
													SET    status = 'WHITE'
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

	public static function getBoardInfo() {
		try {
			$db = DB::getConnection();
			$st = $db->prepare("SELECT *
													FROM   games
													WHERE  (black_name = :username OR white_name = :username)
															   AND status IN ('WHITE', 'BLACK');");
			$st->execute(array('username' => $_SESSION['username']));
		}

		catch(PDOException $e) {
			exit('PDO error ' . $e->getMessage());
		}

		$row = $st->fetch();

		if($row === false) { return false; }

		$boardInfo = [];

		if($row['status'] === 'WHITE') {
			$boardInfo['turn'] = 'white';
		}

		else if($row['status'] === 'BLACK') {
			$boardInfo['turn'] = 'black';
		}

		if($row['white_name'] === $_SESSION['username']) {
			$boardInfo['colour'] = 'white';
			$boardInfo['opponentName'] = $row['black_name'];
		}

		else if($row['black_name'] === $_SESSION['username']) {
			$boardInfo['colour'] = 'black';
			$boardInfo['opponentName'] = $row['white_name'];
		}

		$boardInfo['myName'] = $_SESSION['username'];

		$boardInfo['positions'] = $row['board'];

		return $boardInfo;
	}


	public static function movePiece($oldX, $oldY, $newX, $newY) {

		try {
			$db = DB::getConnection();
			$st = $db->prepare("SELECT *
													FROM   games
													WHERE  (black_name = :username OR white_name = :username)
															   AND status IN ('WHITE', 'BLACK');");
			$st->execute(array('username' => $_SESSION['username']));
		}

		catch(PDOException $e) {
			exit('PDO error ' . $e->getMessage());
		}

		$row = $st->fetch();

		if($row === false) { return false; }

		$board = explode(';', $row['board']);
		for($i = 0; $i < sizeof($board); $i++) {
			$board[$i] = str_split($board[$i]);
		}

		if($row['white_name'] === $_SESSION['username'] && $row['status'] === 'WHITE') {
			$updatedBoard = CheckersService::boardAfterWhiteMove($board, $oldX, $oldY, $newX, $newY);
			if($updatedBoard !== false) {
				CheckersService::updateBoard(CheckersService::boardToString($updatedBoard), 'BLACK');
			}
		}

		else if($row['black_name'] === $_SESSION['username'] && $row['status'] === 'BLACK') {
			$updatedBoard = CheckersService::boardAfterBlackMove($board, $oldX, $oldY, $newX, $newY);
			if($updatedBoard !== false) {
				CheckersService::updateBoard(CheckersService::boardToString($updatedBoard), 'WHITE');
			}
		}

		return false;
	}

	public static function boardAfterWhiteMove($positions, $oldX, $oldY, $newX, $newY) {

		if (CheckersService::getColour($positions[$oldX][$oldY]) !== 'white') {
			return false;
		}

		if ($newX === $oldX - 1 && $newY === $oldY - 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			//Promote white
			if ($newX === 0) {
				$positions[$newX][$newY] = 'B';
			}
			return $positions;
		}

		if ($newX === $oldX - 1 && $newY === $oldY + 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			//Promote white
			if ($newX === 0) {
				$positions[$newX][$newY] = 'B';
			}
			return $positions;
		}

		if ($newX === $oldX - 2 && $newY === $oldY - 2 && $positions[$newX][$newY] === 'E'
			&& CheckersService::getColour($positions[$oldX - 1][$oldY - 1]) === 'black') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX - 1][$oldY - 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			//Promote white
			if ($newX === 0) {
				$positions[$newX][$newY] = 'B';
			}
			return $positions;
		}

		if ($newX === $oldX - 2 && $newY === $oldY + 2 && $positions[$newX][$newY] === 'E'
			&& CheckersService::getColour($positions[$oldX - 1][$oldY + 1]) === 'black') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX - 1][$oldY + 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			//Promote white
			if ($newX === 0) {
				$positions[$newX][$newY] = 'B';
			}
			return $positions;
		}

		if($positions[$oldX][$oldY] === 'B') {

			if($newX === $oldX + 1 && $newY === $oldY - 1 && $positions[$newX][$newY] === 'E') {
				$positions[$newX][$newY] = $positions[$oldX][$oldY];
				$positions[$oldX][$oldY] = 'E';
				return $positions;
			}

			if($newX === $oldX + 1 && $newY === $oldY + 1 && $positions[$newX][$newY] === 'E') {
				$positions[$newX][$newY] = $positions[$oldX][$oldY];
				$positions[$oldX][$oldY] = 'E';
				return $positions;
			}

			if($newX === $oldX + 2 && $newY === $oldY - 2 && $positions[$newX][$newY] === 'E'
				&& CheckersService::getColour($positions[$oldX + 1][$oldY - 1]) === 'black') {
				$positions[$newX][$newY] = $positions[$oldX][$oldY];
				$positions[$oldX + 1][$oldY - 1] = 'E';
				$positions[$oldX][$oldY] = 'E';
				return $positions;
			}

			if($newX === $oldX + 2 && $newY === $oldY + 2 && $positions[$newX][$newY] === 'E'
				&& CheckersService::getColour($positions[$oldX + 1][$oldY + 1]) === 'black') {
				$positions[$newX][$newY] = $positions[$oldX][$oldY];
				$positions[$oldX + 1][$oldY + 1] = 'E';
				$positions[$oldX][$oldY] = 'E';
				return $positions;
			}
		}

		return false;
	}

	public static function boardAfterBlackMove($positions, $oldX, $oldY, $newX, $newY) {

		if (CheckersService::getColour($positions[$oldX][$oldY]) !== 'black') {
			return false;
		}

		if($newX === $oldX + 1 && $newY === $oldY - 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			//Promote black
			if($newX === 7) {
				$positions[$newX][$newY] = 'D';
			}
			return $positions;
		}

		if($newX === $oldX + 1 && $newY === $oldY + 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			if($newX === 7) {
				$positions[$newX][$newY] = 'D';
			}
			return $positions;
		}

		if($newX === $oldX + 2 && $newY === $oldY - 2 && $positions[$newX][$newY] === 'E'
			&& CheckersService::getColour($positions[$oldX + 1][$oldY - 1]) === 'white') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX + 1][$oldY - 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			if($newX === 7) {
				$positions[$newX][$newY] = 'D';
			}
			return $positions;
		}

		if($newX === $oldX + 2 && $newY === $oldY + 2 && $positions[$newX][$newY] === 'E'
			&& CheckersService::getColour($positions[$oldX + 1][$oldY + 1]) === 'white') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX + 1][$oldY + 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			if($newX === 7) {
				$positions[$newX][$newY] = 'D';
			}
			return $positions;
		}

		if($positions[$oldX][$oldY] === 'D') {

			if($newX === $oldX - 1 && $newY === $oldY - 1 && $positions[$newX][$newY] === 'E') {
				$positions[$newX][$newY] = $positions[$oldX][$oldY];
				$positions[$oldX][$oldY] = 'E';
				return $positions;
			}

			if($newX === $oldX - 1 && $newY === $oldY + 1 && $positions[$newX][$newY] === 'E') {
				$positions[$newX][$newY] = $positions[$oldX][$oldY];
				$positions[$oldX][$oldY] = 'E';
				return $positions;
			}

			if($newX === $oldX - 2 && $newY === $oldY - 2 && $positions[$newX][$newY] === 'E'
				&& CheckersService::getColour($positions[$oldX - 1][$oldY - 1]) === 'white') {
				$positions[$newX][$newY] = $positions[$oldX][$oldY];
				$positions[$oldX - 1][$oldY - 1] = 'E';
				$positions[$oldX][$oldY] = 'E';
				return $positions;
			}

			if($newX === $oldX - 2 && $newY === $oldY + 2 && $positions[$newX][$newY] === 'E'
				&& CheckersService::getColour($positions[$oldX - 1][$oldY + 1]) === 'white') {
				$positions[$newX][$newY] = $positions[$oldX][$oldY];
				$positions[$oldX - 1][$oldY + 1] = 'E';
				$positions[$oldX][$oldY] = 'E';
				return $positions;
			}
		}

		return false;
	}

	public static function updateBoard($board, $status) {
		try {
			$db = DB::getConnection();
			$st = $db->prepare("UPDATE games
													SET    board = :board,
																 status = :status
													WHERE  (black_name = :username OR white_name = :username)
															   AND status IN ('WHITE', 'BLACK');");
			$st->execute(array('board' => $board,
												 'status' => $status,
												 'username' => $_SESSION['username']));
		}

		catch(PDOException $e) {
			exit('PDO error ' . $e->getMessage());
		}

	}

	public static function removeEveryGameAssociatedWithCurrentUser() {

		try {
			$db = DB::getConnection();
			$st = $db->prepare("DELETE FROM games 
													WHERE  white_name = :username
																 OR black_name = :username;");
			$st->execute(array('username' => $_SESSION['username']));
		}

		catch(PDOException $e) {
			exit('PDO error ' . $e->getMessage());
		}

	}
};

?>

