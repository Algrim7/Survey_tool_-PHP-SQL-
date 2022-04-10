<?php
	//Mit Datenbank verbinden:
	$conn = mysqli_connect('localhost','Tester','test1');
	if(!$conn){
		echo 'Connection error: ' . mysqli_connect_error();
	}	
?>

<!DOCTYPE html>
<html>
<head>
	<title> Umfrage erstellen </title>
</head>

<body>

	<h4> Bitte geben Sie einen Namen für die Umfrage ein und bestätigen Sie. </h4><br>
	<body>
	

	<form action="init.php" method="post">
	<!-- Eingeben des Umfragetitels -->
	<p><input type="submit" name="bes" value=" Umfrage erstellen " /></p>
	</form>
	</body>
	
<?php
// php-Code um Datenbank zuerstellen
if (isset($_POST['bes'])){
	
	$sql = "CREATE DATABASE Umfrage;";
	if (mysqli_query($conn, $sql)) {
		mysqli_close($conn);
	} else {
		//echo "Error: " . $sql . "<br>" . mysqli_error($conn);
		mysqli_close($conn);
	}
	
	$conn = mysqli_connect('localhost','Tester','test1','Umfrage');
	if(!$conn){
		echo 'Connection error: ' . mysqli_connect_error();
	}
	$sqlT1 = "CREATE TABLE Frage (FrID INT NOT NULL AUTO_INCREMENT, FrTyp TINYINT NOT NULL, FrText TEXT NOT NULL, PRIMARY KEY (FrID));";
	$sqlT2 = "CREATE TABLE Antwort (AntID INT NOT NULL AUTO_INCREMENT, FrID INT NOT NULL, AntText TEXT NOT NULL, PRIMARY KEY (`AntID`));";
	$sqlT3 = "CREATE TABLE Ergebnis (ErgID INT NOT NULL AUTO_INCREMENT, FrID INT NOT NULL, AntID INT NULL, AntEingabeText TEXT NULL, PRIMARY KEY (`ErgID`));";
	$sqlT4 = "ALTER TABLE Antwort ADD FOREIGN KEY (FrID) REFERENCES Frage(FrID) ON DELETE CASCADE ON UPDATE CASCADE;";
	$sqlT5 = "ALTER TABLE Ergebnis ADD FOREIGN KEY (FrID) REFERENCES Frage(FrID) ON DELETE CASCADE ON UPDATE CASCADE;";
	$sqlT6 = "ALTER TABLE Ergebnis ADD FOREIGN KEY (AntID) REFERENCES Antwort(AntID) ON DELETE CASCADE ON UPDATE CASCADE;";
	
	$sql_ALL = [$sqlT1,$sqlT2,$sqlT3,$sqlT4,$sqlT5,$sqlT6];
	
	for($j = 0; $j < count($sql_ALL); $j++){
		if (mysqli_query($conn, $sql_ALL[$j])) {
		} else {
			//echo "Error: " . $sql2 . "<br>" . mysqli_error($conn);
		}
	}
	mysqli_close($conn);
	header("Location: Umfrage_einrichten.php");
	exit();
}
?>
</body>
</html>