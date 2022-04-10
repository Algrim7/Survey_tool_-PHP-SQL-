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
	<title> AKAD Ergebnisse </title>
</head>

<body>

	<h3> Das sind die Ergebnisse der aktuellen Umfrage: </h3> <br>
	
	<form action="ergebnisse.php" method="post">
	<!-- Durchgehen aller Fragen -->
	<?php for($i = 0; $i < count($fragen_array); $i++){?>
		Frage <?php echo ($i+1) ?>: <?php echo $fragen_array[$i][2] ?><br>
		<!-- Durchgehen der Antworten. Dabei muss der Fragentyp beachtet werden! -->
		<?php if ($fragen_array[$i][1] == 1) {
			//SELECT `AntEingabeText` FROM `ergebnisse` WHERE `FrId`=2 and `AntEingabeText` IS NOT NULL
			$sql3 = "SELECT `AntEingabeText` FROM `ergebnis` WHERE `FrId`=" . $fragen_array[$i][0] . "  And `AntEingabeText` IS NOT NULL;";
			$result = mysqli_query($conn, $sql3);
			$res = mysqli_fetch_all($result, MYSQLI_ASSOC);
			$res_String = '';
			for($n = 0; $n < count($res); $n++) {
				$res_String = $res_String . $res[$n]['AntEingabeText'] . ", ";
			}
			echo substr($res_String,0,-2);
			?> <br>
			<!--- <input type="text" name="Frage<?php //echo $fragen_array[$i][0] ?>" /> <br> -->
		<?php } else {?>
			<?php for($j = 0; $j < count($antworten_array); $j++){
				if ($antworten_array[$j][1] == $fragen_array[$i][0]) {
					//Zählen aller Ergebnisse der entsprechenden Antwort
					$sql3 = "SELECT count(*) FROM `ergebnis` WHERE `FrId`=" . $fragen_array[$i][0] . " AND `AntId`=" . $antworten_array[$j][0] . ";";
					$result = mysqli_query($conn, $sql3);
					$res = mysqli_fetch_all($result, MYSQLI_ASSOC);
					?> 
					<?php print_r($antworten_array[$j][2].":\t". $res[0]['count(*)']) ?><br>
				<?php } ?>
			<?php } ?>
		<?php } ?>	<br>
	<?php } ?>
	<!-- Eingabe absenden -->
	<input type="submit" name="back" value="Zurück">
	</form>
	
	<?php
	if (isset($_POST['back'])){
		header("Location: Umfrage_einrichten.php");
		exit();
	}
	?>


</body>
</html>