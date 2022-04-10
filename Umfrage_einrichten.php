<?php

	//Mit Datenbank verbinden:
	$conn = mysqli_connect('localhost','Tester','test1','Umfrage');
	if(!$conn){
		echo 'Connection error: ' . mysqli_connect_error();
	}
	
?>

<!DOCTYPE html>
<html>
<head>
	<title> Fragenstellen </title>
</head>

<body>

	<h4> Bitte geben Sie im Folgenden die neue Frage ein: </h4><br>
	<body>
	

	<form action="Umfrage_einrichten.php" method="post">
	<!-- Abfragen welcher Fragentyp gewünscht ist (Dropdown-Menü)-->
	Fragentyp:
	<select name="Typ">
	<option value="Eingabe">Eingabefeld</option>
	<option value="MPC">Multiple Choice</option>
	<!-- Eingeben der Frage -->
	</select>
	<p><h5>Frage:</h5><input type="text" name="Frage" /></p>
	<!-- Mögliche Antworten für Multiple Choice-Frage -->
	<h5>Antworten:</h5>
	Wenn dies eine Frage mit Eingabefeld sein soll, dann müssen keine Antworten vorgegeben werden!
	<p>1:<br /><input type="text" name="Antwort1" /></p>
	<p>2:<br /><input type="text" name="Antwort2" /></p>
	<p>3:<br /><input type="text" name="Antwort3" /></p>
	<p>4:<br /><input type="text" name="Antwort4" /></p>
	<p>5:<br /><input type="text" name="Antwort5" /></p>
	<p>6:<br /><input type="text" name="Antwort6" /></p>
	<p>7:<br /><input type="text" name="Antwort7" /></p>
	<p>8:<br /><input type="text" name="Antwort8" /></p>
	<p>9:<br /><input type="text" name="Antwort9" /></p>
	<p>10:<br /><input type="text" name="Antwort10" /></p>
	<p><input type="submit" name="einreichen" value=" Einreichen " />
	<input type="submit" name="weiter" value=" Zur Umfrage " />
	<input type="submit" name="results" value=" Ergebnisse " /></p>
	</form>
	</body>
	
<?php
// php-Code um Frage in Datenbank einzutragen
// Fragennummer der letzten Frage erhalten
$sql = 'SELECT MAX(FrId) FROM frage';
$result = mysqli_query($conn, $sql);
$frId = mysqli_fetch_all($result, MYSQLI_ASSOC);
if (array_values($frId)[0]['MAX(FrId)'] == NULL) {
	$frId_next = 1;
} else {
	$frId_next = array_values($frId)[0]['MAX(FrId)'] + 1;
}
// Ermitteln des Fragentyps
if (isset($_POST['einreichen'])){
	// Wenn es eine Eingabe-Frage sein soll, kann die Frage direkt übergeben werden:
	if ($_POST['Typ']=="Eingabe"){
		$sqlFr = "INSERT INTO `frage` (`FrID`, `FrTyp`, `FrText`) VALUES ('" . $frId_next . "', '1', '" . $_POST['Frage'] . "')";
		mysqli_query($conn, $sqlFr);
	} else {
		// Wenn es eine Multiple Choice-Frage ist, kann die Frage auch einfach in die Datenbank eingetragen werden. Es müssen aber zusätzlich noch die Antworten berücksichtig werden
		$sqlFr = "INSERT INTO `frage` (`FrID`, `FrTyp`, `FrText`) VALUES ('" . $frId_next . "', '2', '" . $_POST['Frage'] . "')";
		mysqli_query($conn, $sqlFr);
		for($i = 1; $i < 11; $i++){
			$str = 'Antwort' . $i;
			if ($_POST[$str] != NULL){
				$sqlAnt = "INSERT INTO `antwort` (`AntId`, `FrID`, `AntText`) VALUES (NULL, '" . $frId_next . "', '" . $_POST[$str] . "')";
				mysqli_query($conn, $sqlAnt);
			}
		}
	}
}
mysqli_close($conn);

	
//Weiter zu der Umfrage
if (isset($_POST['weiter'])){
	header("Location: umfrage.php");
	exit();
}

//Weiter zu den Ergebnissen
if (isset($_POST['results'])){
	header("Location: ergebnisse.php");
	exit();
}
?>
	
	
</body>
</html>