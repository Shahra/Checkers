<?php

class User
{
	protected $id, $name, $surname, $password;

	function __construct( $id, $name, $surname, $password )
	{
		$this->id = $id;
		$this->name = $name;
		$this->surname = $surname;
		$this->password = $password;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>

