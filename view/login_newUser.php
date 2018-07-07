<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8" />
	<title>Login</title>
</head>
<body>
	<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=login/processNewUser">
        Odaberite korisničko ime: <input type="text" name="username" /> <br />
        Odaberite lozinku: <input type="password" name="password" /> <br />
        Vaša e-mail adresa: <input type="text" name="email" /> <br />
        <button type="submit">Stvori korisnički račun!</button>
    </form>

    <p>
        Povratak na <a href="index.php">početnu stranicu</a>.
    </p>

    <?php
        echo $message;
    ?>
</body>
</html>
