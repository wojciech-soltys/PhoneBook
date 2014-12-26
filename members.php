<?php
include ('session.php'); // Includes Login Script

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
?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="custom.css" rel="stylesheet" type="text/css">
<script src="jquery-2.1.3.js"></script>
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
           				<input name="createMember" onclick="window.location.href='createMember.php';" value="Dodaj członka" class="redButton" type="button"/>
       				</p>
				</div>	
			</div>
		</div>
	</div>
	<div id="site-container">
		<h3 class="colour blue">Statystyki</h3>
		<label> </label>
			<?php
			date_default_timezone_set('UTC');
			$year = date("Y");
			$query = "SELECT count(id) FROM `Members` WHERE id > 0";
			$result = mysql_query($query);
			$currentDate = date("Y-m-d");
			echo "<label>Liczba członków w antenie: </label>" . mysql_result($result, 0);
			if ($currentDate > $year."-10-01") {
				$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND id IN (
						SELECT userID
						FROM `Payments`
						WHERE (type = 2 AND paymentDate > STR_TO_DATE('" . $year . "-10-01','%Y-%m-%d') AND paymentDate < STR_TO_DATE('" . ($year+1) . "-03-01','%Y-%m-%d'))
						OR (type = 3 AND paymentDate > STR_TO_DATE('" . $year . "-01-01','%Y-%m-%d') AND paymentDate < STR_TO_DATE('" . ($year+1) . "-01-01','%Y-%m-%d'))
					)";
			} else {
				$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND id IN (
						SELECT userID
						FROM `Payments`
						WHERE (type = 1 AND paymentDate > STR_TO_DATE('" . $year . "-03-01','%Y-%m-%d') AND paymentDate < STR_TO_DATE('" . $year . "-10-01','%Y-%m-%d'))
						OR (type = 3 AND paymentDate > STR_TO_DATE('" . $year . "-01-01','%Y-%m-%d') AND paymentDate < STR_TO_DATE('" . ($year+1) . "-01-01','%Y-%m-%d'))
					)";
			}
			$result = mysql_query($query);
			echo "<br><label>Liczba członków z aktualnie opłaconą składką: </label>" . mysql_result($result, 0);
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND aegeeEmail = 1";
			$result = mysql_query($query);
			echo "<br><label>Liczba członków posiadających adres w domenie aegee-gliwice.org: </label>" . mysql_result($result, 0);
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND declaration = 1";
			$result = mysql_query($query);
			echo "<br><label>Liczba członków, którzy wypełnili ankietę: </label>" . mysql_result($result, 0);
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND connectedToList = 1";
			$result = mysql_query($query);
			echo "<br><label>Liczba członków podłączonych do listy ogólnej AEGEE Gliwice: </label>" . mysql_result($result, 0);
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND mentorID = 0";
			$result = mysql_query($query);
			echo "<br><label>Liczba mentorów: </label>" . mysql_result($result, 0) . "<br>";
			?>
			
		<h3 class="colour blue">Lista członków</h3>
		<table class="hovered">
			<col width="1%">
			<col width="11%">
			<col width="11%">
			<col width="8%">
			<col width="15%">
			<col width="7%">
			<col width="13%">
			<col width="5%">
			<col width="5%">
			<col width="22%">
			<tr>
				<th class='center'>Lp.</th>
				<th class='center'>Imię</th>
				<th class='center'>Nazwisko</th>
				<th class='center'>Numer telefonu</th>
				<th class='center'>Prywatny adres email</th>
				<th class='center'>@aegee-gliwice.org</th>
				<th class='center'>Numer karty członkowskiej</th>
				<th class='center'>Deklaracja</th>
				<th class='center'>Podłączenie do listy</th>
				<th class='center'>Mentor</th>
			</tr>
			<?php 
			$index = 1;
			$query = "SELECT * FROM `Members` WHERE id > 0";
			$result = mysql_query($query);
			while ( $row = mysql_fetch_array($result) ) {
				echo "<tr onclick='details(".$row["id"].")'>";
				echo "<td>" . $index++ . "</td>";
				echo "<td>" . $row["firstName"] . "</td>";
				echo "<td>" . $row["lastName"] . "</td>";
				echo "<td>" . $row["phone"]  . "</td>";
				echo "<td>" . $row["privateEmail"]  . "</td>";
				if ($row["aegeeEmail"] == 1) {
					echo "<td class='center'><input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled checked/></td>";
				} else {
					echo "<td class='center'><input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled/></td>";
				}
				echo "<td>" . $row["cardNumber"]  . "</td>";
				if ($row["declaration"] == 1) {
					echo "<td class='center'><input type='checkbox' name='declaration' value='declaration' disabled checked/></td>";
				} else {
					echo "<td class='center'><input type='checkbox' name='declaration' value='declaration' disabled/></td>";
				}
				if ($row["connectedToList"] == 1) {
					echo "<td class='center'><input type='checkbox' name='connectedToList' value='connectedToList' disabled checked/></td>";
				} else {
					echo "<td class='center'><input type='checkbox' name='connectedToList' value='connectedToList' disabled/></td>";
				}
				$query = "SELECT firstName, lastName FROM `Members` WHERE id = ".$row["mentorID"];
				$mentorResult = mysql_query($query);
				$mentor = mysql_fetch_array($mentorResult);
				if ($row["mentorID"] != 0 && $row["mentorID"] != -1) {
					echo "<td>" . $mentor["firstName"] . " " . $mentor["lastName"] . "</td>";
				} else if ($row["mentorID"] == 0){
					echo "<td><b>" . $mentor["firstName"] . "</b>" . "</td>";
				} else if ($row["mentorID"] == -1){
					echo "<td>" . $mentor["firstName"] . "</td>";
				}
				echo "</tr>";
			}
			?>
		</table>
		<script>
			function details(id) {
				$("#selectedId").val(id);
				$("#details").submit();
			}
		</script>
		<form id="details" method="post" action="selectedMember.php">
    		<input type="hidden" id="selectedId" name="selectedId" value="0">
		</form>
	</div>
	</div>
</body>
</html>