<?php


/**
 * Klasa koja se brine o svemu vezanom s bazom onlines.
 */
class InviteService{

	public function napravi_igru($username_igraca_kojeg_zovemo, $nas_username) {
		try {
			$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO games(username_bijelog, username_crnog, board, status) VALUES '.
					'(:username_bijelog, :username_crnog, :board, :status)');
			$st->execute(array('username_bijelog' => $nas_username, 'username_crnog' => $username_igraca_kojeg_zovemo,
			 'board' => "ECECECEC;CECECECE;ECECECEC;EEEEEEEE;EEEEEEEE;AEAEAEAE;EAEAEAEA;AEAEAEAE", 'status' => 'request_sent'));
		}
		catch( PDOException $e ) { exit( 'Greska u InviteService/ocisti_pitanje: ' . $e->getMessage() ); }

	}

	public function imam_li_poziv($username) {
		try {
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM games WHERE (status=:request) AND (username_crnog=:username)' );
			$st->execute(array( 'request' => 'request_sent', 'username' => $username));
		}

		catch( PDOException $e ) { exit( 'Greska u OnlineService/ocisti_pitanje: ' . $e->getMessage() ); }
		if($st->rowCount() > 0) {
			return $st->fetch();
		} else {
			return false;
		}
	}

	public function odbij_poziv($usernameBijelog, $mojUsername) {
		try {
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM games WHERE (status=:request) AND (username_bijelog=:usernamebijelog) AND (username_crnog=:usernamecrnog)');
			$st->execute( array( 'request' => "request_sent", 'usernamecrnog' => $mojUsername, "usernamebijelog" => $usernameBijelog));
		}

		catch( PDOException $e ) { exit( 'Greska u OnlineService/ocisti_pitanje: ' . $e->getMessage() ); }

	}

	public function prihvati_poziv($usernameBijelog, $mojUsername) {
		try {
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE games SET status=:request WHERE (username_bijelog=:usernamebijelog) AND (username_crnog=:usernamecrnog)');
			$st->execute( array( 'request' => "active", 'usernamecrnog' => $mojUsername, "usernamebijelog" => $usernameBijelog));
		}

		catch( PDOException $e ) { exit( 'Greska u OnlineService/ocisti_pitanje: ' . $e->getMessage() ); }

	}
};

?>
