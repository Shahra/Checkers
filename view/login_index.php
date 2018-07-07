<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8" />
	<title>Login</title>
</head>
<body>
	<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=login/processLogin">
        Korisničko ime: <input type="text" name="username" /> <br />
        Lozinka: <input type="password" name="password" /> <br />
        <button type="submit">Ulogiraj se!</button>
    </form>

    <p>
        Ako nemate korisnički račun, otvorite ga <a href="<?php echo __SITE_URL; ?>/index.php?rt=login/newUser">ovdje</a>.
    </p>

	<?php
        echo $message;
    ?>
</body>
</html>
