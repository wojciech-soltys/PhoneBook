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

$ses_sql = mysql_query("SELECT member_id FROM Members, Login WHERE username='$user_check'", $connection);
$ses_row = mysql_fetch_array($ses_sql);
$selectedId = $ses_row["member_id"];
if (isset($_POST ['savePayment'])) {
	$auditCU = $ses_row["member_id"];
}
$ses_sql = mysql_query("SELECT type from Members WHERE id='" . $ses_row["member_id"] . "'", $connection);
$ses_row = mysql_fetch_array($ses_sql);
$userType = $ses_row["type"];
if ($userType == 'Z' || $userType == 'R') {
	if (isset($_POST ['selectedId'])) {
		$selectedId = $_POST ['selectedId'];
	}
}
if (isset($_POST ['removeMember'])) {
	$remId = $_POST ['removeId'];
	$query = "UPDATE `Members` SET old = 1 WHERE id=" . $remId;
	$retval = mysql_query($query, $connection);
	if(!$retval) {
		die('Błąd podczas zapisu danych: ' . mysql_error());
	}
	echo "<script type='text/javascript'>alert('Członek został usunięty');</script>";
} else if (isset($_POST ['addMember'])) {
	$addId = $_POST ['addId'];
	$query = "UPDATE `Members` SET old = 0 WHERE id=" . $addId;
	$retval = mysql_query($query, $connection);
	if(!$retval) {
		die('Błąd podczas zapisu danych: ' . mysql_error());
	}
	echo "<script type='text/javascript'>alert('Członek został przywrócony');</script>";
} else if (isset($_POST ['savePayment'])) {
	$paymentDate = '';
	$paymentType = '';
	$paymentYear = '';
	$paymentAmount = '';
	
	$paymentDate = $_POST ['paymentDate'];
	$paymentDate = stripslashes($paymentDate);
	$paymentDate = mysql_real_escape_string($paymentDate);
	
	$paymentType = $_POST ['paymentType'];
	$paymentType = stripslashes($paymentType);
	$paymentType = mysql_real_escape_string($paymentType);
	
	$paymentYear = $_POST ['paymentYear'];
	$paymentYear = stripslashes($paymentYear);
	$paymentYear = mysql_real_escape_string($paymentYear);
	
	$paymentAmount = $_POST ['paymentAmount'];
	$paymentAmount = stripslashes($paymentAmount);
	$paymentAmount = mysql_real_escape_string($paymentAmount);
	
	$errorPaymentDate = ((preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $paymentDate) == 0) ? 'Niepoprawna wartość w polu data płatności. Wpisz wartość według schematu: RRRR-MM-DD.' : '');
	if ((strlen($errorPaymentDate) == 0)) {
		$currentDate = date("Y-m-d");
		$member_id = $_POST ['selectedId'];
		$query = "INSERT INTO Payments (member_id, amount, date, type, year, auditCD, auditCU) VALUES ($member_id,'$paymentAmount','$paymentDate','$paymentType','$paymentYear','$currentDate',$auditCU)";
		$retval = mysql_query($query, $connection);
		if(! $retval )
		{
			die('Błąd podczas zapisu danych: ' . mysql_error());
		}
		echo "<script type='text/javascript'>alert('Dane zostały poprawnie zapisane');</script>";
	}
	else {
		echo "<script>alert('Zapis danych nie powiódł się')</script>";
	}
}
if (isset($_POST ['addPayment'])) {
	date_default_timezone_set('UTC');
	$year = date("Y");
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
				<li class="active">
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
		echo "<tr><td colspan=\"2\"></td><td><form id=\"details\" method=\"post\" action=\"selectedMemberEdit.php\">";
		echo "<input type=\"hidden\" id=\"selectedId\" name=\"selectedId\" value=\"$selectedId\">";
		if ($userType == 'Z') {
			echo "<br><input name=\"submit\" value=\"Edytuj dane\" class=\"blueButton\" type=\"submit\"/>";
		}
		echo "</form></td>";
		if ($userType == 'Z') {
			if ($row["old"] == 0) {
				echo "<td><form id=\"details2\" method=\"post\" action=\"selectedMember.php\">";
				echo "<input type=\"hidden\" id=\"removeId\" name=\"removeId\" value=\"$selectedId\">";
				echo "<br><input name=\"removeMember\" value=\"Usuń członka\" class=\"blueButton\" type=\"submit\"/></td></tr>";
			} else if ($row["old"] == 1) {
				echo "<td><form id=\"details2\" method=\"post\" action=\"selectedMember.php\">";
				echo "<input type=\"hidden\" id=\"addId\" name=\"addId\" value=\"$selectedId\">";
				echo "<br><input name=\"addMember\" value=\"Przywróć członka\" class=\"blueButton\" type=\"submit\"/></td></tr>";
			}
		}
		echo "</table>";
	?>
	</div>
	<div id="site-container">
		<h3 class="colour blue">Składki członkowskie</h3>
		<?php 
		$query = "SELECT date, type, year, amount FROM `Payments` WHERE member_id=$selectedId ORDER BY date DESC";
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
			if (isset($_POST ['addPayment'])){
				echo "<tr>";
				echo "<td class='center'>" . $index++ . "</td>";
				$currentDate = date("Y-m-d");
				echo "<td class='center'><input type=\"text\" id=\"paymentDate\" size=\"10\" maxlength=\"10\" name=\"paymentDate\" value=\"$currentDate\"></td>";
				echo "<td class='center'>
						<select name='paymentType' id='paymentType' onchange='changeAmount();'>
							<option value=\"1\">Semestr 1</option>
							<option value=\"2\">Semestr 2</option>
							<option value=\"3\">Rok</option>
						</select>
					</td>"; 
				echo "<td class='center'>
						<select name='paymentYear' id='paymentYear'>
							<option value=\"".($year - 1)."\">".($year - 1)."</option>
							<option selected value=\"".$year."\">".$year."</option>
							<option value=\"".($year + 1)."\">".($year + 1)."</option>
						</select>
					</td>";
				echo "<td class='center'>
						<select name='paymentAmount' id='paymentAmount'>
							<option value=\"20\">20,00 zł</option>
							<option value=\"40\">40,00 zł</option>
						</select>
					</td>";
				echo "</tr>";
			?>
			<script>
				function changeAmount() {
					if ($('#paymentType').val() == '1' || $("#paymentType").val() == '2') {
						$('#paymentAmount').val('20');
					} else if ($('#paymentType').val() == '3') {
						$('#paymentAmount').val('40');
					}
				}
			</script>
			<?php 
			}
			echo "</table>";
			if (isset($_POST ['addPayment'])){
				echo "<input type=\"hidden\" id=\"selectedId\" name=\"selectedId\" value=\"$selectedId\">";
				echo "<br><input name=\"savePayment\" value=\"Zapisz\" style=\"margin-left: 70%;\" class=\"blueButton\" type=\"submit\"/>";
				echo "</form>";
			}
		}
		if (!isset($_POST ['addPayment'])) {
			if ($userType == 'Z') {
				echo "<form id=\"addPayment\" method=\"post\" action=\"selectedMember.php\">";
				echo "<input type=\"hidden\" id=\"selectedId\" name=\"selectedId\" value=\"$selectedId\">";
				echo "<br><input name=\"addPayment\" value=\"Dodaj składkę\" style=\"margin-left: 70%;\" class=\"blueButton\" type=\"submit\"/>";
				echo "</form>";
			}
		}
		echo "<br>";
	?>
	<br>
	</div>
	</div>
	<?php 
	mysql_close($connection);
	?>
</body>
</html>