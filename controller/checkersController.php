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
};

?>
