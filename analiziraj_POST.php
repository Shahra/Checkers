<?php

// Pomocne funkcije za analiziranje podataka poslanih preko POST.

function analiziraj_POST_login()
{
	// Analizira $_POST iz forme za login

	if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) )
	{
		crtaj_formaZaLogin( 'Trebate unijeti korisničko ime i lozinku.' );
		exit();
	}

	if( !preg_match( '/^[a-zA-Z]{3,10}$/', $_POST['username'] ) )
	{
		crtaj_formaZaLogin( 'Korisničko ime treba imati između 3 i 10 slova.' );
		exit();
	}

	// Dakle dobro je korisničko ime.
	// Provjeri taj korisnik postoji u bazi; dohvati njegove ostale podatke.
	$db = DB::getConnection();

	try
	{
		$st = $db->prepare( 'SELECT username, password, has_registered FROM UserList WHERE username=:username' );
		$st->execute( array( 'username' => $_POST['username'] ) );
	}
	catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

	$row = $st->fetch();

	if( $row === false )
	{
		crtaj_formaZaLogin( 'Korisnik s tim imenom ne postoji.' );
		exit();
	}
	else if( $row['has_registered'] === '0' )
	{
		crtaj_formaZaLogin( 'Korisnik s tim imenom se nije još registrirao. Provjerite e-mail.' );
		exit();
	}
	else if( !password_verify( $_POST['password'], $row['password'] ) )
	{
		crtaj_formaZaLogin( 'Lozinka nije ispravna.' );
		exit();
	}
	else
	{
		// Sad je valjda sve OK. Ulogiraj ga.
		$_SESSION['username'] = $_POST['username'];
		crtaj_ulogiraniKorisnik();
		exit();
	}

}


function analiziraj_POST_novi()
{
	// Analizira $_POST iz forme za stvaranje novog korisnika

	if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) || !isset( $_POST['email'] ) )
	{
		crtaj_formaZaNovogKorisnika( 'Trebate unijeti korisničko ime, lozinku i e-mail adresu.' );
		exit();
	}

	if( !preg_match( '/^[A-Za-z]{3,10}$/', $_POST['username'] ) )
	{
		crtaj_formaZaNovogKorisnika( 'Korisničko ime treba imati između 3 i 10 slova.' );
		exit();
	}
	else if( !filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL) )
	{
		crtaj_formaZaNovogKorisnika( 'E-mail adresa nije ispravna.' );
		exit();
	}
	else
	{
		// Provjeri jel već postoji taj korisnik u bazi
		$db = DB::getConnection();

		try
		{
			$st = $db->prepare( 'SELECT * FROM UserList WHERE username=:username' );
			$st->execute( array( 'username' => $_POST['username'] ) );
		}
		catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

		if( $st->rowCount() !== 0 )
		{
			// Taj user u bazi već postoji
			crtaj_formaZaNovogKorisnika( 'Korisnik s tim imenom već postoji u bazi.' );
			exit();
		}

		// Dakle sad je napokon sve ok.
		// Dodaj novog korisnika u bazu. Prvo mu generiraj random string od 10 znakova za registracijski link.
		$reg_seq = '';
		for( $i = 0; $i < 20; ++$i )
			$reg_seq .= chr( rand(0, 25) + ord( 'a' ) ); // Zalijepi slučajno odabrano slovo

		try
		{
			$st = $db->prepare( 'INSERT INTO UserList(username, password, email, reg_seq, has_registered) VALUES ' .
				                '(:username, :password, :email, :reg_seq, 0)' );

			$st->execute( array( 'username' => $_POST['username'],
				                 'password' => password_hash( $_POST['password'], PASSWORD_DEFAULT ),
				                 'email' => $_POST['email'],
				                 'reg_seq'  => $reg_seq ) );
		}
		catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

		// Sad mu još pošalji mail
		$to       = $_POST['email'];
		$subject  = 'Registracijski mail';
		$message  = 'Poštovani ' . $_POST['username'] . "!\nZa dovršetak registracije kliknite na sljedeći link: ";
		$message .= 'http://' . $_SERVER['SERVER_NAME'] . htmlentities( dirname( $_SERVER['PHP_SELF'] ) ) . '/register.php?niz=' . $reg_seq . "\n";
		$headers  = 'From: rp2@studenti.math.hr' . "\r\n" .
		            'Reply-To: rp2@studenti.math.hr' . "\r\n" .
		            'X-Mailer: PHP/' . phpversion();

		$isOK = mail($to, $subject, $message, $headers);

		if( !$isOK )
			exit( 'Greška: ne mogu poslati mail. (Pokrenite na rp2 serveru.)' );

		// Zahvali mu na prijavi.
		crtaj_zahvalaNaPrijavi();
		exit();
	}
}

?>
