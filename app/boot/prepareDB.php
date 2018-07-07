<?php

require_once 'db.class.php';

$db = DB::getConnection();

$has_tables = false;

try {
	$st = $db->prepare( 
		'SHOW TABLES LIKE :tblname'
	);

	$st->execute(array('tblname' => 'users'));
	if($st->rowCount() > 0) {
		$has_tables = true;
	}

	$st->execute(array('tblname' => 'games'));
	if($st->rowCount() > 0) {
		$has_tables = true;
	}

}
catch(PDOException $e) {
	exit("PDO error [show tables]: " . $e->getMessage());
}


if( $has_tables ) {
	exit('Tablice users / games vec postoje. Obrisite ih pa probajte ponovno.');
}

try {
	$st = $db->prepare( 
		'CREATE TABLE IF NOT EXISTS users (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'username varchar(20) NOT NULL,' .
		'password_hash varchar(255) NOT NULL,'.
		'email varchar(50) NOT NULL,' .
		'registration_sequence varchar(20) NOT NULL,' .
		'has_registered int DEFAULT 0,' .
		'online int DEFAULT 0,' .
		'last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)'
	);
	$st->execute();
}
catch(PDOException $e) {
	exit("PDO error [create users]: " . $e->getMessage());
}

echo "Napravio tablicu users.<br />";

// Ubaci neke korisnike unutra
try {
	$st = $db->prepare('INSERT INTO users(username, password_hash, email, registration_sequence, has_registered) VALUES (:username, :password, \'a@b.com\', \'abc\', 1)' );

	$st->execute(array('username' => 'zeljko', 'password' => password_hash('zeljko', PASSWORD_DEFAULT)));
	$st->execute(array('username' => 'matija', 'password' => password_hash('matija', PASSWORD_DEFAULT)));
	$st->execute(array('username' => 'ivan', 'password' => password_hash('ivan', PASSWORD_DEFAULT)));
	$st->execute(array('username' => 'stjepan', 'password' => password_hash('stjepan', PASSWORD_DEFAULT)));
}
catch(PDOException $e) { exit("PDO error [insert users]: " . $e->getMessage()); }

echo "Ubacio u tablicu users.<br />";

?> 
