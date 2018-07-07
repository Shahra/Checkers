<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=books/searchResults">
	Unesi ime autora čije te knjige zanimaju:
	<input type="text" name="author" />

	<button type="submit">Traži</button>
</form>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
