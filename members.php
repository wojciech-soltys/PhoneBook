<?php
include ('session.php'); // Includes Login Script

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
if (!mysql_select_db($databaseName,$connection)) {
	echo '<script language="javascript">';
	echo 'alert("Błąd otwarcia bazy danych");';
	echo '</script>';
	exit (0);
}
$ses_sql = mysql_query("SELECT member_id FROM Members, Login WHERE username='$user_check'", $connection);
$ses_row = mysql_fetch_array($ses_sql);
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
				<a href="#"><img src="logo.png"></a>	
			</div>
			<div id="site-header-right">		
				<div id="intranet_login">
					<p>
						Zalogowany: <?php echo $login_session;?>
					</p>
				</div>	
			</div>
		</div>
				<hr>
	<header class="bg-light">
	<div class="navigation-bar light">
		<div class="navigation-bar-content container">
			<ul class="element-menu">
				<li class="active">
					<a href="#">Lista członków</a>
				</li>
				<?php  if ($ses_row["type"] == 'Z') {?>
				<li>
					<a href="javascript:window.location.href='createMember.php';">Dodaj członka</a>
				</li>
				<?php }?>
				<li>
					<a href="javascript:window.location.href='oldMembers.php';">Byli członkowie</a>
				</li>
				<li>
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
		<h3 class="colour blue">Statystyki</h3>
		<table class="form">
			<col width="1%">
			<col width="45%">
			<col width="54%">
			<?php
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND old = '0'";
			$result = mysql_query($query);
			echo "<tr><td></td><td><label>Liczba członków w antenie</label></td><td>" . mysql_result($result, 0) . "</td></tr>";
			date_default_timezone_set('UTC');
			$currentDate = date("Y-m-d");
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND old = '0' AND id IN (
					SELECT member_id
					FROM `Payments`
					WHERE expiration_date >= STR_TO_DATE('". ($currentDate) ."' ,'%Y-%m-%d'))";
			$result = mysql_query($query);
			echo "<tr><td></td><td><label>Liczba członków z aktualnie opłaconą składką</label></td><td>" . mysql_result($result, 0) . "</td></tr>";
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND old = '0' AND aegeeEmail = 1";
			$result = mysql_query($query);
			echo "<tr><td></td><td><label>Liczba członków posiadających adres w domenie aegee-gliwice.org</label></td><td>" . mysql_result($result, 0) . "</td></tr>";
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND old = '0' AND declaration = 1";
			$result = mysql_query($query);
			echo "<tr><td></td><td><label>Liczba członków, którzy wypełnili deklarację członkowską</label></td><td>" . mysql_result($result, 0) . "</td></tr>";
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND old = '0' AND connectedToList = 1";
			$result = mysql_query($query);
			echo "<tr><td></td><td><label>Liczba członków podłączonych do listy ogólnej AEGEE Gliwice</label></td><td>" . mysql_result($result, 0) . "</td></tr>";
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND old = '0' AND mentor_id = 0";
			$result = mysql_query($query);
			echo "<tr><td></td><td><label>Liczba mentorów</label></td><td>" . mysql_result($result, 0) . "</td></tr>";
			?>
		</table>
		<h3 class="colour blue">Lista członków</h3>
		<table class="hovered">
			<col width="1%">
			<col width="12%">
			<col width="12%">
			<col width="8%">
			<col width="15%">
			<col width="18%">
			<col width="5%">
			<col width="5%">
			<col width="20%">
			<col width="4%">
			<tr>
				<th class=''>Lp.</th>
				<th class=''>Imię</th>
				<th class=''>Nazwisko</th>
				<th class=''>Numer telefonu</th>
				<th class=''>Prywatny adres e-mail</th>
				<th class=''>Numer karty członkowskiej</th>
				<th class=''>Deklaracja</th>
				<th class=''>@aegee-gliwice.org<br>Podłączenie do listy</th>
				<th class=''>Mentor</th>
			</tr>
			<?php 
			$index = 1;
			$indexFile = 1;
			$query = "SELECT * FROM `Members` WHERE id > 0 AND old = '0' ORDER BY lastName";
			$result = mysql_query($query);
			while ( $row = mysql_fetch_array($result) ) {
				date_default_timezone_set('UTC');
				$currentDate = date("Y-m-d");
				$query_fee = "SELECT true FROM `Members`, `Payments` WHERE Members.id = " . $row["id"] . " AND Members.id = Payments.member_id 
					AND expiration_date >= STR_TO_DATE('". ($currentDate) ."' ,'%Y-%m-%d')";
				$fee = @mysql_num_rows(mysql_query($query_fee));
				if ($fee == 1 || $row["type"] == 'H') {				
					if ($row["type"] == 'Z') {
						echo "<tr title='Członek Zarządu' bgcolor='#ddefff' onclick='details(".$row["id"].")'>";
					}  else if ($row["type"] == 'R') {
						echo "<tr title='Członek Komisji Rewizyjnej' bgcolor='#ebf5ff' onclick='details(".$row["id"].")'>";
					} else if ($row["type"] == 'K') {
						echo "<tr title='Koordynator' bgcolor='#f3f9ff' onclick='details(".$row["id"].")'>";
					} else {
						echo "<tr onclick='details(".$row["id"].")'>";
					}
				} else {
					echo "<tr title='Brak opłaconej składki!' bgcolor='#ffbebe' onclick='details(".$row["id"].")'>";
				}
				echo "<td>" . $index++ . "</td>";
				echo "<td>" . $row["firstName"] . "</td>";
				echo "<td>" . $row["lastName"] . "</td>";
				echo "<td><a href=\"tel:" . $row["phone"] . "\">" . $row["phone"]  . "</a></td>";
				echo "<td><a href=\"mailto:" . $row["privateEmail"]  . "\">" . $row["privateEmail"]  . "</a></td>";
				echo "<td>" . $row["cardNumber"]  . "</td>";
				if ($row["declaration"] == 1) {
					echo "<td class='center'><input type='checkbox' name='declaration' value='declaration' disabled checked/></td>";
				} else {
					echo "<td class='center'><input type='checkbox' name='declaration' value='declaration' disabled/></td>";
				}
				if ($row["connectedToList"] == 1) {
					echo "<td class='center'><input type='checkbox' name='connectedToList' value='connectedToList' disabled checked/><br>";
				} else {
					echo "<td class='center'><input type='checkbox' name='connectedToList' value='connectedToList' disabled/><br>";
				}
				if ($row["aegeeEmail"] == 1) {
					echo "<input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled checked/></td>";
				} else {
					echo "<input type='checkbox' name='aegeeEmail' value='aegeeEmail' disabled/></td>";
				}
				$query = "SELECT firstName, lastName FROM `Members` WHERE id = ".$row["mentor_id"];
				$mentorResult = mysql_query($query);
				$mentor = mysql_fetch_array($mentorResult);
				if ($row["mentor_id"] != 0 && $row["mentor_id"] != -1) {
					echo "<td>" . $mentor["firstName"] . " " . $mentor["lastName"] . "</td>";
				} else if ($row["mentor_id"] == 0){
					echo "<td><b>" . $mentor["firstName"] . "</b>" . "</td>";
				} else if ($row["mentor_id"] == -1){
					echo "<td>" . $mentor["firstName"] . "</td>";
				}
				echo "</tr>";
			}
			?>
		</table>
		<br>
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
	<?php
	mysql_close($connection);
	?>
</body>
</html>