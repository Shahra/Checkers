<?php

class User
{
	protected $username, $password_hash, $email, $registration_sequence, $has_registered, $online;

	function __construct($username, $password_hash, $email, $registration_sequence, $has_registered, $online) {
		$this->username = $username;
		$this->password_hash = $password_hash;
		$this->email = $email;
		$this->registration_sequence = $registration_sequence;
		$this->has_registered = $has_registered;
		$this->online = $online;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>

