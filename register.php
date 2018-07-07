<?php 

require_once 'crtaj_html.php';
require_once 'db.class.php';

session_start();


// Ova skripta analizira $_GET['niz'] i u bazi postavlja has_registered=1 za onog korisnika koji ima taj niz.
// Jako je mala šansa da dvojica imaju isti.

if( !isset( $_GET['niz'] ) || !preg_match( '/^[a-z]{20}$/', $_GET['niz'] ) )
	exit( 'Nešto ne valja s nizom.' );

// Nađi korisnika s tim nizom u bazi
$db = DB::getConnection();

try
{
	$st = $db->prepare( 'SELECT * FROM UserList WHERE reg_seq=:reg_seq' );
	$st->execute( array( 'reg_seq' => $_GET['niz'] ) );
}
catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

$row = $st->fetch();

if( $st->rowCount() !== 1 )
	exit( 'Taj registracijski niz ima ' . $st->rowCount() . 'korisnika, a treba biti točno 1 takav.' );
else
{
	// Sad znamo da je točno jedan takav. Postavi mu has_registered na 1.
	try
	{
		$st = $db->prepare( 'UPDATE UserList SET has_registered=1 WHERE reg_seq=:reg_seq' );
		$st->execute( array( 'reg_seq' => $_GET['niz'] ) );
	}
	catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

	// Sve je uspjelo, zahvali mu na registraciji.
	crtaj_zahvalaNaRegistraciji();
	exit();
}

?>
