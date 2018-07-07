<?php

class CheckersController extends BaseController {
	public function index() {
		$this->registry->template->title = 'Currently online users: ';
		$this->registry->template->show('checkers_index');
	}

	public function game() {
		$this->registry->template->show('checkers_game');
	}
};

?>
