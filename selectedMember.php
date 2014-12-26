<?php
include ('session.php');

$databaseAddress = 'db101.nano.pl:3306';
$databaseName = 'db4_aegee_pl';
$databaseUser = 'usr019691';
$databasePassword = 'aegee_20702';
if ( !mysql_connect($databaseAddress, $databaseUser, $databasePassword) ) {
	echo '<script language="javascript">';
	echo 'alert("Błąd połączenia z baza danych");';
	echo '</script>';
	exit (0);
}
mysql_query("SET NAMES utf8");
if (!mysql_select_db($databaseName)) {
	echo '<script language="javascript">';
	echo 'alert("Błąd otwarcia bazy danych");';
	echo '</script>';
	exit (0);
}
$query = "SELECT * FROM `Members` WHERE id=".$_POST["selectedId"];
$result = mysql_query($query);
?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="custom.css" rel="stylesheet" type="text/css">
<style>
#site-wrapper {
  background: #fff;
}
</style>
</head>
<body home page page-id-131 page-template-default rf_wrapper>
	<div id="site-wrapper">
	<div class="wrapper" id="site-header-wrapper">
		<div id="site-header" class="wrapper-content">
			<div id="site-logo">
				<a href="/"><img src="logo.png"></a>	
			</div>
			<div id="site-header-right">		
				<div id="intranet_login">
					<p class="login_title">Portal członków</p>
					<form class="" action="logout.php" method="post" enctype="multipart/form-data">
						<p style="display: inline-block;width: 202px;">
							Zalogowany: <?php echo $login_session; ?>
						</p>
            			<p style="display: inline-block;">
           					<input name="submit" value="Wyloguj" class="redButton" type="submit"/>
       					</p>
           			</form>
           				<p>
           					<input name="submit" value="Dodaj członka" class="redButton" type="submit"/>
       					</p>
				</div>	
			</div>
		</div>
	</div>
	<div id="site-container">
	<?php 
		$row = mysql_fetch_array($result);
		echo "<h3 class=\"colour blue\">Dane osobowe członka " . $row["firstName"] . " " . $row["lastName"] . "</h3>";
		echo "<label>Id: </label>" . $row["id"] . "<br>";
		echo "<label>Imię: </label>" . $row["firstName"] . "<br>";
		echo "<label>Nazwisko: </label>" . $row["lastName"] . "<br>";
		echo "<label>Data wstąpienia: </label>" . $row["accessionDate"] . "<br>";
		echo "<label>Numer telefonu: </label>" . $row["phone"] . "<br>";
		echo "<label>Prywatny adres email: </label>" . $row["privateEmail"] . "<br>";
		if ($row["aegeeEmail"] == 1) {
			echo "<label>Adres w domenie aegee-gliwice.org: </label><input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled checked/><br>";
		} else {
			echo "<label>Adres w domenie aegee-gliwice.org: </label><input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled/><br>";
		}
		echo "<label>Data urodzenia: </label>" . $row["birthDate"] . "<br>";
		echo "<label>Numer karty członkowskiej: </label>" . $row["cardNumber"] . "<br>";
		if ($row["declaration"] == 1) {
			echo "<label>Deklaracja: </label><input type='checkbox' name='declaration' value='declaration' disabled checked/><br>";
		} else {
			echo "<label>Deklaracja: </label><input type='checkbox' name='declaration' value='declaration' disabled/><br>";
		}
		if ($row["connectedToList"] == 1) {
			echo "<label>Podłączenie do listy ogólnej: </label><input type='checkbox' name='connectedToList' value='connectedToList' disabled checked/><br>";
		} else {
			echo "<label>Podłączenie do listy ogólnej: </label><input type='checkbox' name='connectedToList' value='connectedToList' disabled/><br>";
		}
		if ($row["mentorID"] != 0 && $row["mentorID"] != -1) {
			$query = "SELECT firstName, lastName FROM `Members` WHERE id = ".$row["mentorID"];
			$mentorResult = mysql_query($query);
			$mentor = mysql_fetch_array($mentorResult);
			echo "<label>Mentor: </label>" . $mentor["firstName"] . " " . $mentor["lastName"] . "<br>";
		} else if ($row["mentorID"] == 0){
			echo "<label>Mentor: </label>" . "<b>Mentor</b>" . "<br>";
		} else if ($row["mentorID"] == -1){
			echo "<label>Mentor: </label>" . "-" . "<br>";
		}	
	?>
	</div>
	<div id="site-container">
		<h3 class="colour blue">Składki członkowskie</h3>
		<?php 
		$query = "SELECT paymentDate, type, amount FROM `Payments` WHERE userID=".$_POST["selectedId"];
		$result = mysql_query($query);
		$rowCount = mysql_num_rows($result);
		if ($rowCount == 0) {
			echo "Brak płatności";
		}
		else {
			$index = 1;
			echo "<table class='hovered'>";
			echo "<tr>";
			echo "<th class='center'>Lp.</th>";
			echo "<th class='center'>Data płatności</th>";
			echo "<th class='center'>Rodzaj składki</th>";
			echo "<th class='center'>Kwota</th>";
			echo "</tr>";
			while ( $row = mysql_fetch_array($result) ) {
				echo "<tr>";
				echo "<td class='center'>" . $index++ . "</td>";
				echo "<td class='center'>" . $row["paymentDate"] . "</td>";
				if ($row["type"] == 1) {
					echo "<td class='center'>Roczna</td>";
				} else {
					echo "<td class='center'>Semestralna</td>";
				}
				echo "<td class='center'>" . $row["amount"] . "</td>";
				echo "</tr>";
			}
		}
		?>
	</div>
	</div>
</body>
</html>