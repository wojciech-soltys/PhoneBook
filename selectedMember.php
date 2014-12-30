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

$ses_sql = mysql_query("SELECT members_id FROM Members, Login WHERE username='$user_check'", $connection);
$ses_row = mysql_fetch_array($ses_sql);
$selectedId = $ses_row["members_id"];
$ses_sql = mysql_query("SELECT type from Members WHERE id='" . $ses_row["members_id"] . "'", $connection);
$ses_row = mysql_fetch_array($ses_sql);
if ($ses_row["type"] == 'Z' || $ses_row["type"] == 'R') {
	if (isset($_POST ['selectedId'])) {
		$selectedId = $_POST ['selectedId'];
	}
}
$query = "SELECT * FROM `Members` WHERE id=$selectedId";
$result = mysql_query($query);
?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
<script src="jquery-2.1.3.js"></script>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="custom.css" rel="stylesheet" type="text/css">
<style>
#site-wrapper {
  background: #fff;
}
</style>
</head>
<body>
	<div id="site-wrapper">
	<div class="wrapper" id="site-header-wrapper">
		<div id="site-header" class="wrapper-content">
			<div id="site-logo">
				<a href="javascript:window.location.href='members.php';"><img src="logo.png"></a>	
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
           					<input name="listMembers" onclick="window.location.href='members.php';" value="Lista członków" class="redButton" type="button"/>
           					<?php  if ($ses_row["type"] == 'Z') {?>
           						<input name="createMember" onclick="window.location.href='createMember.php';" value="Dodaj członka" class="redButton" type="button"/>
       						<?php }?>
       					</p>
				</div>	
			</div>
		</div>
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
		echo "<h3 class=\"colour blue\">Dane osobowe członka " . $row["firstName"] . " " . $row["lastName"] . " - $type</h3>";
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
		if ($ses_row["type"] == 'Z') {
			echo "<form id=\"details\" method=\"post\" action=\"selectedMemberEdit.php\">";
    		echo "<input type=\"hidden\" id=\"selectedId\" name=\"selectedId\" value=\"$selectedId\">";
    		echo "<br><input name=\"submit\" value=\"Edytuj dane\" class=\"redButton\" type=\"submit\"/>";
			echo "</form>";
		}
		if (!isset($_POST ['selectedId'])) {
			echo "<br><input name=\"changePassword\" onclick=\"window.location.href='changePassword.php';\" value=\"Zmień hasło\" class=\"redButton\" type=\"button\"/><br>";
		}
		echo "<br>";
	?>
	</div>
	<div id="site-container">
		<h3 class="colour blue">Składki członkowskie</h3>
		<?php 
		$query = "SELECT paymentDate, type, amount FROM `Payments` WHERE userID=$selectedId";
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
					echo "<td class='center'>Semestr 1</td>";
				} else if ($row["type"] == 2) {
					echo "<td class='center'>Semestr 2</td>";
				} else if ($row["type"] == 3) {
					echo "<td class='center'>Rok</td>";
				}
				echo "<td class='center'>" . number_format((float)$row["amount"], 2, ',', '') . "</td>";
				echo "</tr>";
			}
			echo "</table>";
		}
		echo "<br>";
		?>
	</div>
	</div>
	<?php 
	mysql_close($connection);
	?>
</body>
</html>