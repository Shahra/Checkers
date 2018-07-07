<?php
    if (!LoginService::loggedIn()) {
        header('Location: ' . __SITE_URL . '/index.php?rt=login/index');
        exit();
    }

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8">
	<title>Checkers</title>
	<link rel="stylesheet" href="<?php echo __SITE_URL;?>/css/style.css">
</head>
<body>
	<h1><?php echo $title; ?></h1>

	<nav>
        <ul>
            <li><a href="<?php echo __SITE_URL; ?>/index.php?rt=login/logout">Logout</a></li>
        </ul>
	</nav>
