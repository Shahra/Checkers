<?php

class Book
{
	protected $id, $author, $title;

	function __construct( $id, $author, $title )
	{
		$this->id = $id;
		$this->author = $author;
		$this->title = $title;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
