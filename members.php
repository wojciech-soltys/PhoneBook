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

if ($ses_row["type"] == 'Z') {
	date_default_timezone_set('UTC');
	$year = date("Y");
	$currentDate = date("Y-m-d");
	if (($currentDate >= $year."-05-25" && $currentDate < $year."-07-01") || ($currentDate >= $year."-11-20" && $currentDate < $year."-12-10")) {
		$agoraTime = 1;
	} else {
		$agoraTime = 0;
	}
	$fn = "membersList.txt";
	$file = fopen($fn, "w");
	if ($agoraTime == 1) {
		$fnA = "membersListAgora.txt";
		$fileA = fopen($fnA, "w");
		fwrite($fileA, "Tworzenie listy członków na potrzeby Krajowego Rejestru Sądowego:\r\nPo pobraniu pliku membersListAgora.txt należy skopiować poniższe dane do pliku Word.\r\n");
		fwrite($fileA, "Następnie z menu Wstawianie należy rozwinąć menu Tabela i wybrać Konwertuj tekst na tabelę.\r\nW okienku, które się pojawiło, należy zwiększyć liczbę kolumn o jedną (będzie przeznaczona na podpis) oraz należy ustawić separator tekstu na średnik.\r\n");
		fwrite($fileA, "Na koniec należy wstawić tło pisma firmowego, nadać tytuł liście oraz dodać tytuł kolumny Podpis oraz ułożyć dokument tak, aby wyglądął ładnie.\r\n");
		fwrite($fileA, "Dane do skopiowania:\r\n");
		fwrite($fileA, "Lp.;Imię;Nazwisko;Numer telefonu\r\n");
	} else {
		$fnA = "membersListAgora.txt";
		$fileA = fopen($fnA, "w");
		fclose($fileA);
	}
}
?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
<script src="jquery-2.1.3.js"></script>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="custom.css" rel="stylesheet" type="text/css">
<link rel="Shortcut icon" href="http://aegee-gliwice.org/strona/wp-content/uploads/2013/12/favicon.png" />
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
			if ($currentDate >= $year."-10-01" ) {
				$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND id IN (
						SELECT member_id
						FROM `Payments`
						WHERE (type = 2 AND (year = '".$year."' OR date < STR_TO_DATE('" . ($year+1) . "-03-01','%Y-%m-%d')))
						OR (type = 3 AND year = '".$year."')
					)";
			} else {
				$query = "SELECT count(id) FROM `Members` WHERE id > 0 AND id IN (
						SELECT member_id
						FROM `Payments`
						WHERE (type = 1 AND year = '".$year."')
						OR (type = 3 AND year = '".$year."')
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
			<col width="12%">
			<col width="12%">
			<col width="8%">
			<col width="15%">
			<col width="18%">
			<col width="5%">
			<col width="5%">
			<col width="24%">
			<tr>
				<th class='center'>Lp.</th>
				<th class='center'>Imię</th>
				<th class='center'>Nazwisko</th>
				<th class='center'>Numer telefonu</th>
				<th class='center'>Prywatny adres email</th>
				<th class='center'>Numer karty członkowskiej</th>
				<th class='center'>Deklaracja<br>Aktywność</th>
				<th class='center'>@aegee-gliwice.org<br>Podłączenie do listy</th>
				<th class='center'>Mentor</th>
			</tr>
			<?php 
			$index = 1;
			$indexFile = 1;
			$query = "SELECT * FROM `Members` WHERE id > 0 ORDER BY lastName";
			$result = mysql_query($query);
			while ( $row = mysql_fetch_array($result) ) {
				date_default_timezone_set('UTC');
				$year = date("Y");
				$currentDate = date("Y-m-d");
				if ($currentDate > $year."-10-01") {
					$query_fee = "SELECT true FROM `Members`, `Payments` WHERE Members.id = " . $row["id"] . " AND Members.id = Payments.member_id AND (
						(Payments.type = 2 AND (Payments.year = '".$year."' OR Payments.date < STR_TO_DATE('" . ($year+1) . "-03-01','%Y-%m-%d')))
						OR (Payments.type = 3 AND Payments.year = '".$year."')
					)";
				} else {
					$query_fee = "SELECT true FROM `Members`, `Payments` WHERE Members.id = " . $row["id"] . " AND Members.id = Payments.member_id AND (
						(Payments.type = 1 AND Payments.year = '".$year."')
						OR (Payments.type = 3 AND Payments.year = '".$year."')
					)";
				}
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
				if ($ses_row["type"] == 'Z' && $fee == 1) {
					fwrite($file, $indexFile . ". " . $row["firstName"] . " " . $row["lastName"] . "\r\n");
					if ($agoraTime == 1) {
						fwrite($fileA, $indexFile . ";" . $row["firstName"] . ";" . $row["lastName"] . ";" . $row["phone"] . "\r\n");
					}
					$indexFile++;
				}
				echo "<td>" . $index++ . "</td>";
				echo "<td>" . $row["firstName"] . "</td>";
				echo "<td>" . $row["lastName"] . "</td>";
				echo "<td>" . $row["phone"]  . "</td>";
				echo "<td>" . $row["privateEmail"]  . "</td>";
				echo "<td>" . $row["cardNumber"]  . "</td>";
				if ($row["declaration"] == 1) {
					echo "<td class='center'><input type='checkbox' name='declaration' value='declaration' disabled checked/><br>";
				} else {
					echo "<td class='center'><input type='checkbox' name='declaration' value='declaration' disabled/><br>";
				}
				if ($row["active"] == 1) {
					echo "<input type='checkbox' name='active' value='active' disabled checked/></td>";
				} else {
					echo "<input type='checkbox' name='active' value='active' disabled/></td>";
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
		<div>
			Lista członków AEGEE Gliwice z opłaconą składką generowana na potrzeby tworzenia listy członków mających dostęp do biura:
			&nbsp;<a class="redButton" href="membersList.txt" download>Lista członków</a>
		</div>
		
		<div>
			Lista członków AEGEE Gliwice z opłaconą składką generowana na potrzeby Walnego Zgromadzenia Członków w czerwcu i w grudniu:
			&nbsp;<a class="redButton" href="membersListAgora.txt" download>Lista członków</a>
			<br>
			Uwaga! Lista na potrzeby Walnego Zgromadzenia Członków jest generowana tylko w okresie od 25 maja do 1 lipca oraz 20 listopada do 10 grudnia.
		</div>
		<br>
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
	fclose($file);
	if ($agoraTime == 1) {
		fclose($fileA);
	}
	mysql_close($connection);
	?>
</body>
</html>