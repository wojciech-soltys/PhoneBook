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
				<a href="#"><img src="logo.png"></a>	
			</div>
			<div id="site-header-right">		
				<div id="intranet_login">
					<p class="login_title">Portal członków</p>
					<form class="" action="logout.php" method="post" enctype="multipart/form-data">
						<p style="display: inline-block;width: 202px;">
							Zalogowany: <?php echo $login_session;?>
						</p>
            			<p style="display: inline-block;">
           					<input name="submit" value="Wyloguj" class="redButton" type="submit"/>
       					</p>
           			</form>
           			<p>
           			<?php  if ($ses_row["type"] == 'Z') {?>
           				<input name="createMember" onclick="window.location.href='createMember.php';" value="Dodaj członka" class="redButton" type="button"/>
       				<?php }?>
       					<input name="myData" onclick="window.location.href='selectedMember.php';" value="Moje dane" class="redButton" type="button"/>
       				</p>
				</div>	
			</div>
		</div>
	</div>
	<div id="site-container">
	<?php  if ($ses_row["type"] == 'Z' || $ses_row["type"] == 'R') {?>
		<h3 class="colour blue">Statystyki</h3>
		<label> </label>
			<?php
			$query = "SELECT count(id) FROM `Members` WHERE id > 0";
			$result = mysql_query($query);
			echo "<label>Liczba członków w antenie: </label>" . mysql_result($result, 0);
			date_default_timezone_set('UTC');
			$year = date("Y");
			$currentDate = date("Y-m-d");
			if ($currentDate >= $year."-01-01" && $currentDate < $year."-03-01") {
				$year = $year - 1;
			}
			if ($currentDate >= $year."-10-01" ) {
				$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND id IN (
						SELECT member_id
						FROM `Payments`
						WHERE (type = 2 AND paymentDate >= STR_TO_DATE('" . $year . "-10-01','%Y-%m-%d') AND paymentDate < STR_TO_DATE('" . ($year+1) . "-03-01','%Y-%m-%d'))
						OR (type = 3 AND paymentDate >= STR_TO_DATE('" . $year . "-01-01','%Y-%m-%d') AND paymentDate <= STR_TO_DATE('" . $year . "-12-31','%Y-%m-%d'))
					)";
			} else {
				$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND id IN (
						SELECT member_id
						FROM `Payments`
						WHERE (type = 1 AND paymentDate >= STR_TO_DATE('" . $year . "-03-01','%Y-%m-%d') AND paymentDate <= STR_TO_DATE('" . $year . "-09-30','%Y-%m-%d'))
						OR (type = 3 AND paymentDate >= STR_TO_DATE('" . $year . "-01-01','%Y-%m-%d') AND paymentDate <= STR_TO_DATE('" . $year . "-12-31','%Y-%m-%d'))
					)";
			}
			$result = mysql_query($query);
			echo "<br><label>Liczba członków z aktualnie opłaconą składką: </label>" . mysql_result($result, 0);
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND aegeeEmail = 1";
			$result = mysql_query($query);
			echo "<br><label>Liczba członków posiadających adres w domenie aegee-gliwice.org: </label>" . mysql_result($result, 0);
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND declaration = 1";
			$result = mysql_query($query);
			echo "<br><label>Liczba członków, którzy wypełnili deklarację członkowską: </label>" . mysql_result($result, 0);
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND connectedToList = 1";
			$result = mysql_query($query);
			echo "<br><label>Liczba członków podłączonych do listy ogólnej AEGEE Gliwice: </label>" . mysql_result($result, 0);
			$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND mentor_id = 0";
			$result = mysql_query($query);
			echo "<br><label>Liczba mentorów: </label>" . mysql_result($result, 0) . "<br>";
			?>
		<br>
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
				date_default_timezone_set('UTC');
				$year = date("Y");
				$currentDate = date("Y-m-d");
				if ($currentDate >= $year."-01-01" && $currentDate < $year."-03-01") {
					$year = $year - 1;
				}
				if ($currentDate > $year."-10-01") {
					$query_fee = "SELECT true FROM `Members`, `Payments` WHERE Members.id = " . $row["id"] . " AND Members.id = Payments.member_id AND (
						(Payments.type = 2 AND paymentDate >= STR_TO_DATE('" . $year . "-10-01','%Y-%m-%d') AND paymentDate < STR_TO_DATE('" . ($year+1) . "-03-01','%Y-%m-%d'))
						OR (Payments.type = 3 AND paymentDate >= STR_TO_DATE('" . $year . "-01-01','%Y-%m-%d') AND paymentDate <= STR_TO_DATE('" . $year . "-12-31','%Y-%m-%d'))
					)";
				} else {
					$query_fee = "SELECT true FROM `Members`, `Payments` WHERE Members.id = " . $row["id"] . " AND Members.id = Payments.member_id AND (
						(Payments.type = 1 AND paymentDate >= STR_TO_DATE('" . $year . "-03-01','%Y-%m-%d') AND paymentDate <= STR_TO_DATE('" . $year . "-09-30','%Y-%m-%d'))
						OR (Payments.type = 3 AND paymentDate >= STR_TO_DATE('" . $year . "-01-01','%Y-%m-%d') AND paymentDate <= STR_TO_DATE('" . $year . "-12-31','%Y-%m-%d'))
					)";
				}

				if (@mysql_num_rows(mysql_query($query_fee))==1 || $row["type"] == 'H') {				
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
	<?php } else  if ($ses_row["type"] == 'C' || $ses_row["type"] == 'K' || $ses_row["type"] == 'H') {?>
		<h3 class="colour blue">Książka telefoniczna</h3>
		Wpisz imię i nazwisko członka.
		<form action="members.php" id="member" method="post" enctype="multipart/form-data">
			<div>
				<label>Imię: </label><input type="text" id="firstName" name="firstName" maxlenght="70" value="">
				<label>Nazwisko: </label><input type="text" id="lastName" name="lastName" maxlenght="70" value="">
			</div>
			<br>
			<input name="search_submit" value="Szukaj" class="red" type="submit"/>
		</form>
		<br>
		<?php 
		$errorFirstName = '';
		$errorLastName = '';
		if (isset($_POST ['search_submit'])) {
			$firstName = $_POST ['firstName'];
			$firstName = stripslashes($firstName);
			$firstName = mysql_real_escape_string($firstName);
		
			$lastName = $_POST ['lastName'];
			$lastName = stripslashes($lastName);
			$lastName = mysql_real_escape_string($lastName);
		
			$errorFirstName = ((empty($firstName)) ? 'Pole imię jest puste. ' : '');
			$errorFirstName = ((strlen($firstName) > 70) ? 'Zbyt duża liczba znaków w polu imię.' : '');
			$errorLastName = ((empty($lastName)) ? 'Pole nazwisko jest puste. ' : '');
			$errorLastName = ((strlen($lastName) > 70) ? 'Zbyt duża liczba znaków w polu nazwisko.' : '');
			
			if ((strlen($errorFirstName) == 0) && (strlen($errorLastName) == 0) ) {
				$query = mysql_query("SELECT firstName, lastName, phone from Members WHERE firstName = '$firstName' AND lastName = '$lastName'", $connection);
				$row = mysql_fetch_array($query);
				if (strlen($row["firstName"]) > 0 && strlen($row["lastName"]) > 0 && strlen($row["phone"]) > 0) {
					echo '<h3 class="colour blue">Imię: ' . $row["firstName"] . '&nbsp;&nbsp;&nbsp;&nbsp;Nazwisko: ' . $row["lastName"] . '&nbsp;&nbsp;&nbsp;&nbsp;Numer telefonu: ' . $row["phone"] . '</h3>';
				} else {
					echo '<h3 class="colour blue">Nie znaleziono członka ' . $firstName . ' ' . $lastName . '</h3>';
				}
			}
		}
		?>
		
	<?php }?>
	</div>
	<?php 
	mysql_close($connection);
	?>
</body>
</html>