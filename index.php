<?php 

require_once 'crtaj_html.php';
require_once 'analiziraj_POST.php';
require_once 'db.class.php';

session_start();

// Ako je korisnik već ulogiran, iscrtaj mu odgovarajuću stranicu, a ne formu za login.
if( isset( $_SESSION['username'] ) )
{
	crtaj_ulogiraniKorisnik();
	exit();
}

// Provjeri da li se šalju podaci iz forme.
if( isset( $_POST['username'] ) )
{
	analiziraj_POST_login();
	exit();
}
else
{
	crtaj_formaZaLogin();
	exit();
}

?> 
