<?php

class LibraryService
{
	function getUserById( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, name, surname, password FROM users WHERE id=:id' );
			$st->execute( array( 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false )
			return null;
		else
			return new User( $row['id'], $row['name'], $row['surname'], $row['password'] );
	}


	function getAllUsers( )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, name, surname, password FROM users ORDER BY surname' );
			$st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
		{
			$arr[] = new User( $row['id'], $row['name'], $row['surname'], $row['password'] );
		}

		return $arr;
	}


	function getBookById( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, author, title FROM books WHERE id=:id' );
			$st->execute( array( 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false )
			return null;
		else
			return new Book( $row['id'], $row['author'], $row['title'] );
	}

	function getBooksByAuthorName( $author )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, author, title FROM books WHERE author=:author ORDER BY title' );
			$st->execute( array( 'author' => $author ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
		{
			$arr[] = new Book( $row['id'], $row['author'], $row['title'] );
		}

		return $arr;
	}

	function getAllBooks()
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, author, title FROM books ORDER BY author' );
			$st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
		{
			$arr[] = new Book( $row['id'], $row['author'], $row['title'] );
		}

		return $arr;
	}

};

?>

