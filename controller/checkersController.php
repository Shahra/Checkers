<?php
session_start();
class CheckersController extends BaseController {
	public function index() {
		$this->registry->template->title = ' Checkers!';

		$this->registry->template->show('checkers_index');
	}

	public function game() {
		$this->registry->template->show('checkers_game');
	}
};

?>