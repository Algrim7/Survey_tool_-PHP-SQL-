<?php

	//Mit Datenbank verbinden:
	$conn = mysqli_connect('localhost','Tester','test1','Umfrage');
	if(!$conn){
		echo 'Connection error: ' . mysqli_connect_error();
	}
	
	// Einlesen der Fragen aus der Datenbank
	$sql = 'SELECT FrId, FrTyp, FrText from frage';
	$result = mysqli_query($conn, $sql);
	$fragen_array_sql = mysqli_fetch_all($result, MYSQLI_ASSOC);
	
	// Die Fragen werden in einem 2D Array Gespeichert: [[FrId1,FrTyp1,FrText1],[FrId2,FrTyp2,FrText2],...]
	$fragen_array = [];
	for($i = 0; $i < count($fragen_array_sql); $i++){
		array_push($fragen_array, [$fragen_array_sql[$i]['FrId'],$fragen_array_sql[$i]['FrTyp'],$fragen_array_sql[$i]['FrText']]);
	}
	
	// Einlesen der Antworten aus der Datenbank
	$sql2 = 'SELECT AntId, FrId, AntText from antwort';
	$result = mysqli_query($conn, $sql2);
	$antworten_array_sql = mysqli_fetch_all($result, MYSQLI_ASSOC);
	
	// Die Antworten werden in einem 2D Array Gespeichert: [[AntId1,FrId1,AntText1],[AntId2,FrId2,AntText2],...]
	$antworten_array = [];
	for($i = 0; $i < count($antworten_array_sql); $i++){
		array_push($antworten_array, [$antworten_array_sql[$i]['AntId'],$antworten_array_sql[$i]['FrId'],$antworten_array_sql[$i]['AntText']]);
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title> AKAD Umfrage </title>
</head>

<body>

	<h3> Bitte beantworten Sie die folgenden Fragen! </h3> <br>
	
	<form action="umfrage.php" method="post">
		<!-- Durchgehen aller Fragen -->
		<?php for($i = 0; $i < count($fragen_array); $i++){?>
			Frage <?php echo ($i+1) ?>: <?php echo $fragen_array[$i][2] ?><br>
			<!-- Durchgehen der Antworten. Dabei muss der Fragentyp beachtet werden! -->
			<?php if ($fragen_array[$i][1] == 1) {?>
				<input type="text" name="Frage<?php echo $fragen_array[$i][0] ?>" /> <br>
			<?php } else {?>
				<?php for($j = 0; $j < count($antworten_array); $j++){ 
					if ($antworten_array[$j][1] == $fragen_array[$i][0]) {?> 
						<input type="radio" name="Frage<?php echo $fragen_array[$i][0] ?>" value="Antwort<?php echo $antworten_array[$j][0] ?>" required> <?php echo $antworten_array[$j][2] ?><br>
					<?php } ?>
				<?php } ?>
			<?php } ?>	<br>
		<?php } ?>
		<!-- Eingabe absenden -->
		<input type="submit" name="absenden" value="Absenden">
	
<?php 
	// Eingaben Auswerten und aufbauen eines Ergebnis-Arrays
	// ergebins_array = [[FrId,AntId,AntText],...]
	$ergebnis_array = [];
	if (isset($_POST['absenden'])){
		for($i = 0; $i < count($fragen_array); $i++){
			if ($fragen_array[$i][1] == 1) {
				array_push($ergebnis_array,[$fragen_array[$i][0],NULL,$_POST['Frage'.$fragen_array[$i][0]]]);
			} else {
				if (isset ($_POST["Frage".$fragen_array[$i][0]])){
					for($j = 0; $j < count($antworten_array); $j++){
						if ($antworten_array[$j][1] == $fragen_array[$i][0]) {					
							if ($_POST["Frage".$fragen_array[$i][0]]=="Antwort".$antworten_array[$j][0]){
								array_push($ergebnis_array,[$antworten_array[$j][1],$antworten_array[$j][0],NULL]);
							}
						}
					}
				}
			}
		}
	}
	
	// Letzter Schritt: Vom ergebnis_array in ERGEBNISSE Tabelle:

	$sql3 = "INSERT INTO `ergebnis` (`ErgId`, `FrId`, `AntId`, `AntEingabeText`) VALUES ";
	for($i = 0; $i < count($ergebnis_array); $i++){
		if ($ergebnis_array[$i][1] == NULL) {
			$sql3 = $sql3 . "(NULL, '" . $ergebnis_array[$i][0] . "', NULL, '" . $ergebnis_array[$i][2] . "'), ";
		} else {
			$sql3 = $sql3 . "(NULL, '" . $ergebnis_array[$i][0] . "', '" . $ergebnis_array[$i][1] . "', NULL), ";
		}
	}
	$sql3 = substr($sql3, 0, -2);
	
	if (mysqli_query($conn, $sql3)) {
		mysqli_close($conn);
		header("Location: abgeschlossen.php");
		exit();
	} else {
		//echo "Error: " . $sql3 . "<br>" . mysqli_error($conn);
		mysqli_close($conn);
	}

?>
</form>
</body>
</html>
	