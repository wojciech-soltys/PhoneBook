<?php
include ('session.php');

$databaseAddress = 'db101.nano.pl:3306';
$databaseName = 'db4_aegee_pl';
$databaseUser = 'usr019691';
$databasePassword = 'aegee_20702';
$connection =  mysql_connect($databaseAddress, $databaseUser, $databasePassword);
if (!$connection) {
	echo '<script language="javascript">';
	echo 'alert("Błąd połączenia z baza danych");';
	echo '</script>';
	exit (0);
}
mysql_query("SET NAMES utf8");
if (!mysql_select_db($databaseName, $connection)) {
	echo '<script language="javascript">';
	echo 'alert("Błąd otwarcia bazy danych");';
	echo '</script>';
	exit (0);
}
$query = "SELECT * FROM `Members` WHERE id = $userID";
$result = mysql_query($query);
$ses_sql = mysql_query("SELECT type from Members WHERE id='" . $ses_row["member_id"] . "'", $connection);
$ses_row = mysql_fetch_array($ses_sql);
?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
<script src="jquery-2.1.3.js"></script>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="custom.css" rel="stylesheet" type="text/css">
<link href="metro-bootstrap.css" rel="stylesheet" type="text/css">
<link href="metro-bootstrap-responsive.css" rel="stylesheet" type="text/css">
<link rel="Shortcut icon" href="http://aegee-gliwice.org/strona/wp-content/uploads/2013/12/favicon.png" />
<style>
#site-wrapper {
  background: #fff;
}
</style>
</head>
<body class="metro">
	<div id="site-wrapper">
	<div class="wrapper" id="site-header-wrapper">
		<div id="site-header" class="wrapper-content">
			<div id="site-logo">
				<a href="javascript:window.location.href='members.php';"><img src="logo.png"></a>	
			</div>
			<div id="site-header-right">		
				<div id="intranet_login">
					<p>
						Zalogowany: <?php echo $login_session; ?>
					</p>
				</div>	
			</div>
		</div>
		<hr>
		<header class="bg-light">
	<div class="navigation-bar light">
		<div class="navigation-bar-content container">
			<ul class="element-menu">
				<li>
					<a href="javascript:window.location.href='members.php';">Lista członków</a>
				</li>
				<?php  if ($ses_row["type"] == 'Z') {?>
				<li>
					<a href="javascript:window.location.href='createMember.php';">Dodaj członka</a>
				</li>
				<?php }?>
				<li>
					<a href="javascript:window.location.href='oldMembers.php';">Byli członkowie</a>
				</li>
				<li class="active">
					<a href="javascript:window.location.href='myData.php';">Moje dane</a>
				</li>
				<li>
					<a href="javascript:window.location.href='generateFile.php';">Raport</a>
				</li>
				<li>
					<a id="logout_button" href="#">Wyloguj</a>
				</li>
			</ul>
		</div>
	</div>
	<form id="logout" action="logout.php" method="post" enctype="multipart/form-data">
	</form>
	<script>
	$("#logout_button").click(function() {
		document.getElementById('logout').submit();
    });
	</script>
</header>
	</div>
	<div id="site-container">
	<?php 
		$row = mysql_fetch_array($result);
		switch ($row["type"]) {
			case 'Z' :
				$type = 'członek Zarządu';
				break;
			case 'R' :
				$type = 'członek Komisji Rewizyjnej';
				break;
			case 'K' :
				$type = 'koordynator';
				break;
			case 'H' :
				$type = 'członek honorowy';
				break;
			case 'C' :
				$type = 'członek zwyczajny';
				break;
		}
		echo "<h3 class=\"colour blue\">Moje dane " . $row["firstName"] . " " . $row["lastName"] . " - $type</h3>";
		echo "<table class=\"form\"><col width=\"3%\"><col width=\"35%\"><col width=\"31%\"><col width=\"31%\"><tr><td></td><td><label>Id</label></td><td>" . $row["id"] . "</td><td></td></tr>";
		echo "<tr><td></td><td><label>Imię</label></td><td>" . $row["firstName"] . "</td><td></td></tr>";
		echo "<tr><td></td><td><label>Nazwisko</label></td><td>" . $row["lastName"] . "</td><td></td></tr>";
		echo "<tr><td></td><td><label>Data wstąpienia</label></td><td>" . $row["accessionDate"] . "</td><td></td></tr>";
		echo "<tr><td></td><td><label>Numer telefonu</label></td><td><a href=\"tel:" . $row["phone"] . "\">" . $row["phone"] . "</a></td><td></td></tr>";
		echo "<tr><td></td><td><label>Prywatny adres e-mail</label></td><td><a href=\"mailto:" . $row["privateEmail"]  . "\">" . $row["privateEmail"] . "</a></td><td></td></tr>";
		echo "<tr><td></td><td>";
		if ($row["aegeeEmail"] == 1) {
			echo "<label>Adres w domenie aegee-gliwice.org</label></td><td><input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled checked/>";
		} else {
			echo "<label>Adres w domenie aegee-gliwice.org</label></td><td><input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled/>";
		}
		echo "</td><td></td></tr>";
		echo "<tr><td></td><td><label>Data urodzenia</label></td><td>" . $row["birthDate"] . "</td><td></td></tr>";
		echo "<tr><td></td><td><label>Numer karty członkowskiej</label></td><td>" . $row["cardNumber"] . "</td><td></td></tr>";
		echo "<tr><td></td><td>";
		if ($row["declaration"] == 1) {
			echo "<label>Deklaracja</label></td><td><input type='checkbox' name='declaration' value='declaration' disabled checked/><br>";
		} else {
			echo "<label>Deklaracja</label></td><td><input type='checkbox' name='declaration' value='declaration' disabled/><br>";
		}
		echo "</td><td></td></tr>";
		echo "<tr><td></td><td>";
		if ($row["connectedToList"] == 1) {
			echo "<label>Podłączenie do listy ogólnej</label></td><td><input type='checkbox' name='connectedToList' value='connectedToList' disabled checked/><br>";
		} else {
			echo "<label>Podłączenie do listy ogólnej</label></td><td><input type='checkbox' name='connectedToList' value='connectedToList' disabled/><br>";
		}
		echo "</td><td></td></tr>";
		echo "<tr><td></td><td>";
		if ($row["mentor_id"] != 0 && $row["mentor_id"] != -1) {
			$query = "SELECT firstName, lastName FROM `Members` WHERE id = ".$row["mentor_id"];
			$mentorResult = mysql_query($query);
			$mentor = mysql_fetch_array($mentorResult);
			echo "<label>Mentor</label></td><td>" . $mentor["firstName"] . " " . $mentor["lastName"];
		} else if ($row["mentor_id"] == 0){
			echo "<label>Mentor</label></td><td>" . "<b>Mentor</b>";
		} else if ($row["mentor_id"] == -1){
			echo "<label>Mentor</label></td><td>" . "-";
		}	
		echo "</td><td></td></tr>";
		echo "<tr><td></td><td><label>Członek grup</label></td><td>";
		if ($row["pr"] == 1) { echo "PR "; }
		if ($row["hr"] == 1) { echo "HR "; }
		if ($row["fr"] == 1) { echo "FR "; }
		if ($row["it"] == 1) { echo "IT "; }
		if (($row["pr"] == 0) && ($row["hr"] == 0) && ($row["fr"] == 0) && ($row["it"] == 0)) {
			echo " - ";
		}
		echo "</td><td></td></tr>";
		echo "<tr><td colspan=\"3\"></td><td><input name=\"changePassword\" onclick=\"window.location.href='changePassword.php';\" value=\"Zmień hasło\" class=\"blueButton\" type=\"button\"/></td></tr>";
		echo "</table>";
	?>
	</div>
	<div id="site-container">
		<h3 class="colour blue">Składki członkowskie</h3>
		<?php 
		$query = "SELECT date, type, year, amount FROM `Payments` WHERE member_id=$userID ORDER BY date DESC";
		$result = mysql_query($query);
		$rowCount = mysql_num_rows($result);
		if ($rowCount == 0 && (!isset($_POST ['addPayment']))) {
			echo "Brak płatności";
		}
		else {
			$index = 1;
			if (isset($_POST ['addPayment'])){
				echo "<form id=\"savePayment\" method=\"post\" action=\"selectedMember.php\">";
			}
			echo "<table class='hovered'>";
			echo "<tr>";
			echo "<th class='center'>Lp.</th>";
			echo "<th class='center'>Data płatności</th>";
			echo "<th class='center'>Okres składki</th>";
			echo "<th class='center'>Rok składki</th>";
			echo "<th class='center'>Kwota</th>";
			echo "</tr>";
			while ( $row = mysql_fetch_array($result) ) {
				echo "<tr>";
				echo "<td class='center'>" . $index++ . "</td>";
				echo "<td class='center'>" . $row["date"] . "</td>";
				if ($row["type"] == 1) {
					echo "<td class='center'>Semestr 1</td>";
				} else if ($row["type"] == 2) {
					echo "<td class='center'>Semestr 2</td>";
				} else if ($row["type"] == 3) {
					echo "<td class='center'>Rok</td>";
				}
				echo "<td class='center'>".$row["year"]."</td>";
				echo "<td class='center'>" . number_format((float)$row["amount"], 2, ',', '') . " zł</td>";
				echo "</tr>";
			}
			echo "</table>";
		}
	?>
	<br>
	</div>
	</div>
	<?php 
	mysql_close($connection);
	?>
</body>
</html>