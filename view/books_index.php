<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<table>
	<tr><th>Autor</th><th>Naslov</th></tr>
	<?php 
		foreach( $bookList as $book )
		{
			echo '<tr>' .
			     '<td>' . $book->author . '</td>' .
			     '<td>' . $book->title . '</td>' .
			     '</tr>';
		}
	?>
</table>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>