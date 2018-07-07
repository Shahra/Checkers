<?php

/*
	Input:
		$_GET['vrijemeZadnjegPristupa'] = timestamp kad je bio zadnji pristup

	Output: JSON
		{
			dionice: Polje objekata oblika { oznaka: xxx, ime: xxx, cijena: xxx },
			vrijemeZadnjegPristupa: timestamp kad je bio zadnji pristup
		}
		ili
		{
			error = poruka o grešci.
		}
*/

/*if(!isset( $_GET['vrijemeZadnjegPristupa'] ) )
	sendErrorAndExit( 'Nije postavljeno $_GET["vrijemeZadnjegPristupa"].' );

$zadnjiPristup = (int) $_GET[ 'vrijemeZadnjegPristupa' ];*/

/*while( 1 )
{
	// Provjeri najkasnije vrijeme zadnje promjene
	$db = DB::getConnection();
	$st = $db->prepare( "SELECT MAX(lastModified) AS maxLastModified FROM Dionice" );
	$st->execute();

	$row = $st->fetch();

	$timestamp = strtotime( $row['maxLastModified'] );

	if( $timestamp > $zadnjiPristup )
	{
		// Dohvati sve dionice
		$st = $db->prepare( "SELECT Oznaka, Ime, Cijena FROM Dionice" );
		$st->execute();

		$message = [];
		$message[ 'vrijemeZadnjegPristupa' ] = $timestamp;
		$message[ 'dionice' ] = [];

		while( $row = $st->fetch() )
		{
			$message[ 'dionice' ][] = array( 'ime' => $row['Ime'], 'oznaka' => $row['Oznaka'], 'cijena' => $row['Cijena'] );
		}

		sendJSONandExit( $message );
	}

	// Odspavaj 5 sekundi.
	usleep( 5000000 );
}*/
$fileName = 'games.txt';
$fileContent = file_get_contents($fileName);
$positions = explode( ';', $fileContent );
for($i = 0; $i < sizeof($positions); $i++) {
	$positions[$i] = str_split($positions[$i]);
}
/*echo "\n";
echo "\n";
for($i = 0; $i < 8; $i++) {
	for($j = 0; $j < 8; $j++) {
		echo $positions[$i][$j];
	}
	echo "\n";
}*/



function sendJSONandExit( $message )
{
	// Kao izlaz skripte pošalji $message u JSON formatu i prekini izvođenje.
	header('Content-type:application/json;charset=utf-8');
	echo json_encode( $message );
	flush();
	exit(0);
}


function sendErrorAndExit($messageText)
{
	$message = [];
	$message[ 'error' ] = $messageText;
	sendJSONandExit( $message );
}

//var positions = 'ECECECEC;CECECECE;EDEDEDED;EEEEEEEE;EEEEEEEE;BEBEBEBE;EAEAEAEA;AEAEAEAE';

function getColour($c) {
	if ($c === 'A' || $c === 'B') return "white";
	else if ($c === 'C' || $c === 'D') return "black";
}


function validateMove($positions, $oldX, $oldY, $newX, $newY) {
	if(getColour($positions[$oldX][$oldY]) === 'white') {
		if($newX === $oldX - 1 && $newY === $oldY - 1 && $positions[$newX][$newY] === 'E') {
			echo "True";
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			//Promote white
			if($newX === 0) {
				$positions[$newX][$newY] = 'B';
			}
			return $positions;
		}
		if($newX === $oldX - 1 && $newY === $oldY + 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			//Promote white
			if($newX === 0) {
				$positions[$newX][$newY] = 'B';
			}
			return $positions;
		}
		if($newX === $oldX - 2 && $newY === $oldY - 2 && $positions[$newX][$newY] === 'E'
				 && getColour($positions[$oldX - 1][$oldY - 1]) === 'black') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX - 1][$oldY - 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			//Promote white
			if($newX === 0) {
				$positions[$newX][$newY] = 'B';
			}
			return $positions;
		}
		if($newX === $oldX - 2 && $newY === $oldY + 2 && $positions[$newX][$newY] === 'E'
			&& getColour($positions[$oldX - 1][$oldY + 1]) === 'black') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX - 1][$oldY + 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			//Promote white
			if($newX === 0) {
				$positions[$newX][$newY] = 'B';
			}
			return $positions;
		}
	}
	if(getColour($positions[$oldX][$oldY]) === 'black') {
		if($newX === $oldX + 1 && $newY === $oldY - 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			//Promote black
			if($newX === 7) {
				$positions[$newX][$newY] = 'D';
			}
			return $positions;
		}
		if($newX === $oldX + 1 && $newY === $oldY + 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			if($newX === 7) {
				$positions[$newX][$newY] = 'D';
			}
			return $positions;
		}
		if($newX === $oldX + 2 && $newY === $oldY - 2 && $positions[$newX][$newY] === 'E'
			&& getColour($positions[$oldX + 1][$oldY - 1]) === 'white') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX + 1][$oldY - 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			if($newX === 7) {
				$positions[$newX][$newY] = 'D';
			}
			return $positions;
		}
		if($newX === $oldX + 2 && $newY === $oldY + 2 && $positions[$newX][$newY] === 'E'
			&& getColour($positions[$oldX + 1][$oldY + 1]) === 'white') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX + 1][$oldY + 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			if($newX === 7) {
				$positions[$newX][$newY] = 'D';
			}
			return $positions;
		}
	}
	if($positions[$oldX][$oldY] === 'B') {
		if($newX === $oldX + 1 && $newY === $oldY - 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			return $positions;
		}
		if($newX === $oldX + 1 && $newY === $oldY + 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			return $positions;
		}
		if($newX === $oldX + 2 && $newY === $oldY - 2 && $positions[$newX][$newY] === 'E'
			&& getColour($positions[$oldX + 1][$oldY - 1]) === 'black') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX + 1][$oldY - 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			return $positions;
		}
		if($newX === $oldX + 2 && $newY === $oldY + 2 && $positions[$newX][$newY] === 'E'
			&& getColour($positions[$oldX + 1][$oldY + 1]) === 'black') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX + 1][$oldY + 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			return $positions;
		}
	}
	if($positions[$oldX][$oldY] === 'D') {
		if($newX === $oldX - 1 && $newY === $oldY - 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			return $positions;
		}
		if($newX === $oldX - 1 && $newY === $oldY + 1 && $positions[$newX][$newY] === 'E') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX][$oldY] = 'E';
			return $positions;
		}
		if($newX === $oldX - 2 && $newY === $oldY - 2 && $positions[$newX][$newY] === 'E'
			&& getColour($positions[$oldX - 1][$oldY - 1]) === 'white') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX - 1][$oldY - 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			return $positions;
		}
		if($newX === $oldX - 2 && $newY === $oldY + 2 && $positions[$newX][$newY] === 'E'
			&& getColour($positions[$oldX - 1][$oldY + 1]) === 'white') {
			$positions[$newX][$newY] = $positions[$oldX][$oldY];
			$positions[$oldX - 1][$oldY + 1] = 'E';
			$positions[$oldX][$oldY] = 'E';
			return $positions;
		}
	}
	return false;
}




$oldX = (int) $_GET['oldX']; $oldY = (int) $_GET['oldY']; $newX = (int) $_GET['newX']; $newY = (int) $_GET['newY'];

if(validateMove($positions, $oldX, $oldY, $newX, $newY) !== false) {
	$positions = validateMove($positions, $oldX, $oldY, $newX, $newY);

	$str = "";
	for($i = 0; $i < 8; $i++) {
		for($j = 0; $j < 8; $j++) {
			$str .= $positions[$i][$j];
		}
		if($i !== 7) {
			$str .= ";";
		}
	}


	if( file_put_contents( $fileName, $str ) === false )
		exit( 'Ne mogu pisati u datoteku games.txt' );

	$message['positions'] = $positions;
	sendJSONandExit($message);
}
else {
	sendErrorAndExit("Nije validan potez!");
}


?>
