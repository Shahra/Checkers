<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8" />
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="<?php echo __SITE_URL;?>/css/style.css" />
</head>
<body>
	<h1>Checkers!</h1>
	<div class="page">
  		<div class="form">
			<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=login/processNewUser">
        		<input type="text" name="username" placeholder="Korisničko ime"/> <br />
        		<input type="password" name="password" placeholder="Lozinka"/> <br />
        		<input type="text" name="email" placeholder="E-mail adresa" /> <br />
        		<button type="submit">Stvori korisnički račun!</button>

    			<p class="message">
        		Povratak na <a href="index.php">početnu stranicu</a>.
    			</p>

			</form>
		</div>
	</div>

    <?php
        echo $message;
    ?>
</body>
</html>
