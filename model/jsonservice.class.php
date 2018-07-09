<?php

class JSONService {

	public static function sendJSONandExit($message) {
		header('Content-type:application/json;charset=utf-8');
		echo json_encode($message);
		flush();
		exit(0);
	}

	public static function sendErrorAndExit($messageText) {
		$message = [];
		$message['error'] = $messageText;
		sendJSONandExit($message);
	}
};

?>

