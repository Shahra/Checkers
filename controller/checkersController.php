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

	public function getBoardInfo() {
		$message = CheckersService::getBoardInfo();
		JSONService::sendJSONandExit($message);
	}

	public function movePiece() {
		if(!isset($_GET['oldX']) || !isset($_GET['oldY']) || !isset($_GET['newX']) || !isset($_GET['oldX'])) {
			JSONService::sendErrorAndExit('Error: You have to set up oldX, oldY, newX and newY.');
		}
		$oldX = $_GET['oldX']; $oldY = $_GET['oldY']; $newX = $_GET['newX']; $newY = $_GET['newY'];
		if(!CheckersService::validCoordinate($oldX) || !CheckersService::validCoordinate($oldY) ||
			 !CheckersService::validCoordinate($newX) || !CheckersService::validCoordinate($newY)) {
			JSONService::sendErrorAndExit('Error: Coordinates are not valid.');
		}
		CheckersService::movePiece((int)$_GET['oldX'], (int)$_GET['oldY'], (int)$_GET['newX'], (int)$_GET['newY']);
		JSONService::sendJSONandExit('');
	}

};

?>
