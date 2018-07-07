<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkers</title>
    <link rel="stylesheet" type="text/css" href="<?php echo __SITE_URL;?>/css/style.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
</head>
<body>
	<h1><?php echo $title; ?></h1>

	<nav>
        <ul>
            <!-- TODO ovaj link cemo kasnije morati maknuti, trenutno je samo za testiranje ovdje -->
            <li><a href="<?php echo __SITE_URL; ?>/index.php?rt=checkers/game">Game view</a></li>
            <li><a href="<?php echo __SITE_URL; ?>/index.php?rt=login/logout">Logout</a></li>
        </ul>
	</nav>
<table>
	<tr><th>Online: </th></tr>

<?php  for($i = 0; $i < count($online); $i++) {
	echo "<tr><td>" . $online[$i] . "</td></tr>";
}
?>
</table>

<script>
$("body").on("click", "td", function () {
	var name = $(this).html();
	if (confirm('Challenge ' + name + ' to a game?')) {
    	return;
	} else {
	    return;
	}
});
</script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
