<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<table>
	<tr><th>Ime</th><th>Prezime</th></tr>
	<?php 
		foreach( $userList as $user )
		{
			echo '<tr>' .
			     '<td>' . $user->surname . '</td>' .
			     '<td>' . $user->name . '</td>' .
			     '</tr>';
		}
	?>
</table>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
