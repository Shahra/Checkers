<?php


function crtaj_header()
{
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf8">
		<title>Login</title>
	</head>
	<body>
	<?php
}


function crtaj_footer()
{
	?>
	</body>
	</html>
	<?php
}


function crtaj_formaZaLogin( $errorMsg = '' )
{
	crtaj_header("login.css");
	?>
	<h1 id="naslov">Dama</h1>
	<div class="vr"></div>

	<form method="post" action="<?php echo htmlentities( $_SERVER["PHP_SELF"] ); ?>">
		Korisničko ime:
		<input type="text" name="username" />
		<br />
		Lozinka:
		<input type="password" name="password" />
		<br />
		<button type="submit">Ulogiraj se!</button>
	</form>

	<p>
		Ako nemate korisnički račun, otvorite ga <a href="novi.php">ovdje</a>.
	</p>

	<?php
	if( $errorMsg !== '' )
		echo '<p>Greška: ' . $errorMsg . '</p>';

	crtaj_footer();
}


function crtaj_formaZaNovogKorisnika( $errorMsg = '' )
{
	crtaj_header();
	?>

	<form method="post" action="<?php echo htmlentities( $_SERVER["PHP_SELF"] ); ?>">
		Odaberite korisničko ime:
		<input type="text" name="username" />
		<br />
		Odaberite lozinku:
		<input type="password" name="password" />
		<br />
		Vaša mail-adresa:
		<input type="text" name="email" />
		<br />
		<button type="submit">Stvori korisnički račun!</button>
	</form>

	<p>
		Povratak na <a href="index.php">početnu stranicu</a>.
	</p>

	<?php
	if( $errorMsg !== '' )
		echo '<p>Greška: ' . $errorMsg . '</p>';

	crtaj_footer();
}


function crtaj_ulogiraniKorisnik( $errorMsg = '' )
{
	crtaj_header();
	?>
	<h1>Dama</h1>
	<p>
		Dobrodošli <?php echo $_SESSION['username']; ?>!
	</p>

	<p>
		Sada se možete <a href="logout.php">odlogirati</a>!
	</p>

	<?php
	crtaj_footer();
}


function crtaj_zahvalaNaPrijavi( $errorMsg = '' )
{
	crtaj_header();
	?>

	<p>
		Zahvaljujemo na prijavi. Da biste dovršili registraciju, kliknite na link u mailu kojeg smo Vam poslali.
	</p>

	<p>
		Povratak na <a href="index.php">početnu stranicu</a>.
	</p>


	<?php
	crtaj_footer();
}


function crtaj_zahvalaNaRegistraciji( $errorMsg = '' )
{
	crtaj_header();
	?>

	<p>
		Registracija je uspješno provedena.<br />
		Sada se možete ulogirati na <a href="index.php">početnoj stranici</a>.
	</p>


	<?php
	crtaj_footer();
}

?>
