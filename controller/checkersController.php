<?php
session_start();
require_once __SITE_PATH . '/app/boot/' . 'db.class.php';
class CheckersController extends BaseController {
	public function index() {
		$this->registry->template->title = ' Checkers!';

		$online = array();
		$db = DB::getConnection();
		$st = $db->prepare('SELECT * FROM users');
		$st->execute();
		while($row = $st->fetch()) {
			if($row['online'] == 1 && $row['username'] !== $_SESSION['username']) {
				array_push($online, $row['username']);
			}
		}

		$this->registry->template->online = $online;
		$this->registry->template->show('checkers_index');
	}

	public function game() {
		$this->registry->template->show('checkers_game');
	}
};

?>
