<?php

class DebugService {

	public static function showSession() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		echo '</br>';
			echo '<pre>';
				echo print_r($_SESSION);
			echo '</pre>';
		echo '</br>';
	}

	public static function showPost() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		echo '</br>';
			echo '<pre>';
				echo print_r($_POST);
			echo '</pre>';
		echo '</br>';
	}

};

?>

