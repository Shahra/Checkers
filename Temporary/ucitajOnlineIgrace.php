<?php
require_once '../app/boot/db.class.php';
session_start();

function sendJSONandExit( $message )
{
    header( 'Content-type:application/json;charset=utf-8' );
    echo json_encode( $message );
    flush();
    exit( 0 );
}


function sendErrorAndExit( $messageText )
{
	$message = [];
	$message[ 'error' ] = $messageText;
	sendJSONandExit( $message );
}

if( !isset( $_GET['vrijemeZadnjegPristupa'] ) )
	sendErrorAndExit( 'Nije postavljeno $_GET["vrijemeZadnjegPristupa"].' );

$zadnjiPristup = (int) $_GET[ 'vrijemeZadnjegPristupa' ];

while( 1 )
{
	// Provjeri najkasnije vrijeme zadnje promjene
	$db = DB::getConnection();
	$st = $db->prepare( "SELECT MAX(last_modified) AS maxLastModified FROM users" );
	$st->execute();

	$row = $st->fetch();
	$timestamp = strtotime( $row['maxLastModified'] );

	if( $timestamp > $zadnjiPristup )
	{
		// Dohvati sve dionice
		$st = $db->prepare( "SELECT username FROM users WHERE online LIKE '1' AND username NOT LIKE :username" );
		$st->execute(array("username" => $_SESSION['username']));

		$message = [];
		$message[ 'vrijemeZadnjegPristupa' ] = $timestamp;
		$message[ 'usernames' ] = [];

		while($row = $st->fetch())
		{
			$message[ 'usernames' ][] = array('username' => $row['username']);
		}
		sendJSONandExit($message);
	}

	// Odspavaj 5 sekundi.
	usleep( 5000000 );
}

?>
