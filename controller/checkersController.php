<?php
class CheckersController extends BaseController {
	public function index() {
		$this->registry->template->title = 'Checkers!';
		$this->registry->template->show('checkers_index');
	}

	public function game() {
		$this->registry->template->show('checkers_game');
	}

	public function getOnlinePlayers() {
		$message = CheckersService::getOnlinePlayers();
		JSONService::sendJSONandExit($message);
	}

	public function insideGame() {
		JSONService::sendJSONandExit(CheckersService::insideGame());
	}

	public function challengePlayer() {
		if(!isset($_POST['challengedPlayer'])) {
			JSONService::sendErrorAndExit('Error: There is no challenged player.');
		}
		CheckersService::challengePlayer($_POST['challengedPlayer']);
		JSONService::sendJSONandExit('');
	}

	public function checkIfChallenged() {
		$name = CheckersService::checkIfChallenged();
		JSONService::sendJSONandExit($name);
	}

	public function acceptChallenge() {
		if(!isset($_POST['name'])) {
			JSONService::sendErrorAndExit('Error: There is no challenge to accept.');
		}
		CheckersService::acceptChallenge($_POST['name']);
		JSONService::sendJSONandExit('');
	}

	public function declineChallenge() {
		if(!isset($_POST['name'])) {
			JSONService::sendErrorAndExit('Error: There is no challenge to decline.');
		}
		CheckersService::declineChallenge($_POST['name']);
		JSONService::sendJSONandExit('');
	}

};

?>
