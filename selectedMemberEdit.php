
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
$query = "SELECT * FROM `Members` WHERE id=".$_POST["selectedId"];
$result = mysql_query($query);
$errorFirstName = '';
$errorLastName = '';
$errorAccessionDate = '';
$errorPhone = '';
$errorPrivateEmail = '';
$errorBirthDate = '';
$errorCardNumber = '';

$row = mysql_fetch_array($result);

$id = $row["id"];
$firstName = $row["firstName"];
$lastName = $row["lastName"];
$accessionDate = $row["accessionDate"];
$phone = $row["phone"];
$privateEmail = $row["privateEmail"];
$aegeeEmail = $row["aegeeEmail"];
$birthDate = $row["birthDate"];
$cardNumber = $row["cardNumber"];
$declaration = $row["declaration"];
$connectedToList = $row["connectedToList"];
$mentor_id = $row["mentor_id"];
$type = $row["type"];
$prGroup = $row["pr"];
$hrGroup = $row["hr"];
$frGroup = $row["fr"];
$itGroup = $row["it"];

if (isset($_POST ['submit_changes'])) {
	$firstName = $_POST ['firstName'];
	$firstName = trim($firstName);
	$firstName = stripslashes($firstName);
	$firstName = mysql_real_escape_string($firstName);
	
	$lastName = $_POST ['lastName'];
	$lastName = trim($lastName);
	$lastName = stripslashes($lastName);
	$lastName = mysql_real_escape_string($lastName);
	
	$accessionDate = $_POST ['accessionDate'];
	$accessionDate = trim($accessionDate);
	$accessionDate = stripslashes($accessionDate);
	$accessionDate = mysql_real_escape_string($accessionDate);
	
	$phone = $_POST ['phone'];
	$phone = trim($phone);
	$phone = stripslashes($phone);
	$phone = mysql_real_escape_string($phone);
	
	$privateEmail = $_POST ['privateEmail'];
	$privateEmail = trim($privateEmail);
	$privateEmail = stripslashes($privateEmail);
	$privateEmail = mysql_real_escape_string($privateEmail);
	
	if (isset($_POST ['aegeeEmail'])) {
		$aegeeEmail = 1;
	} else {
		$aegeeEmail = 0;
	}
	
	$birthDate = $_POST ['birthDate'];
	$birthDate = trim($birthDate);
	$birthDate = stripslashes($birthDate);
	$birthDate = mysql_real_escape_string($birthDate);
	
	$cardNumber = $_POST ['cardNumber'];
	$cardNumber = trim($cardNumber);
	$cardNumber = stripslashes($cardNumber);
	$cardNumber = mysql_real_escape_string($cardNumber);
	
	$declaration = isset($_POST['declaration']) ? '1' : '0';
	$connectedToList = isset($_POST['connectedToList']) ? '1' : '0';

	$mentor_id = $_POST ['mentor_id'];
	$type = $_POST ['type'];
	
	$prGroup = isset($_POST['prGroup']) ? '1' : '0';
	$hrGroup = isset($_POST['hrGroup']) ? '1' : '0';
	$frGroup = isset($_POST['frGroup']) ? '1' : '0';
	$itGroup = isset($_POST['itGroup']) ? '1' : '0';

	$errorFirstName = ((empty($firstName)) ? 'Pole imię jest puste. ' : '');
	$errorFirstName = ((strlen($firstName) > 70) ? 'Zbyt duża liczba znaków w polu imię.' : '');
	$errorLastName = ((empty($lastName)) ? 'Pole nazwisko jest puste. ' : '');
	$errorLastName = ((strlen($lastName) > 70) ? 'Zbyt duża liczba znaków w polu nazwisko.' : '');
	$errorAccessionDate = ((preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $accessionDate) == 0) ? 'Niepoprawna wartość w polu data wstąpienia. Wpisz wartość według schematu: RRRR-MM-DD.' : '');
	$errorPhone = ((preg_match("/^[0-9]{9}$/", $phone) == 0) ? 'Niepoprawny numer telefonu.' : ''); 
	$errorPrivateEmail = ((!filter_var($privateEmail, FILTER_VALIDATE_EMAIL)) ? 'Niepoprawny adres poczty elektronicznej.' : '');
	$errorBirthDate = ((preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $birthDate) == 0) ? 'Niepoprawna wartość w polu data urodzenia. Wpisz wartość według schematu: RRRR-MM-DD.' : '');
	$errorCardNumber = ((strlen($cardNumber) < 5) ? 'Niepoprawna wartość w polu numer karty członkowskiej.' : ''); 
	if ((strlen($errorFirstName) == 0) && (strlen($errorLastName) == 0) && (strlen($errorAccessionDate) == 0) && (strlen($errorPhone) == 0) && (strlen($errorPrivateEmail) == 0) && (strlen($errorBirthDate) == 0) && (strlen($errorCardNumber) == 0)) {
		$query = "UPDATE `Members` SET firstName = '$firstName', lastName = '$lastName', accessionDate = '$accessionDate', phone = '$phone', privateEmail = '$privateEmail', aegeeEmail = $aegeeEmail," .
		"birthDate = '$birthDate', cardNumber = '$cardNumber', declaration = $declaration, connectedToList = $connectedToList, mentor_id = $mentor_id, type = '$type', pr = '$prGroup', hr = '$hrGroup', fr = '$frGroup', it = '$itGroup' WHERE id=".$_POST["selectedId"];
		$retval = mysql_query($query, $connection);
		if(! $retval )
		{
			die('Błąd podczas zapisu danych: ' . mysql_error());
		}
		echo "<script type='text/javascript'>alert('Dane zostały poprawnie zapisane');</script>";
		echo "<script>";
		echo "$( document ).ready(function() {";
		echo "goBack();";
		echo "});";
		echo "</script>";
	}
	else {
		echo "<script>alert('Zapis danych nie powiódł się')</script>";
	}
}
?>
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
			
		?>
		<h3 class="colour blue">Zmiana danych osobowych członka <?php echo $row["firstName"] . " " . $row["lastName"]  ?> </h3>
		<form action="selectedMemberEdit.php" id="member" method="post" enctype="multipart/form-data">
		<table class="form">
			<col width="3%">
			<col width="35%">
			<col width="31%">
			<col width="31%">
			<tr>
				<td></td>
				<td><label>Id</label></td>
				<td><?php echo $id ?></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Imię</label></td>
				<td><input type="text" id="firstName" name="firstName" maxlenght="70" value="<?php echo $firstName ?>"></td>
				<td><label class="invalid"><?php echo $errorFirstName; ?></label></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Nazwisko</label></td>
				<td><input type="text" id="lastName" name="lastName" maxlenght="70" value="<?php echo $lastName ?>"></td>
				<td><label class="invalid"><?php echo $errorLastName; ?></label></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Data wstąpienia</label></td>
				<td><input type="text" id="accessionDate" size="10" maxlength="10" name="accessionDate" value="<?php echo $accessionDate ?>"></td>
				<td><label class="invalid"><?php echo $errorAccessionDate; ?></label></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Numer telefonu</label></td>
				<td><input type="text" id="phone" size="9" minlength="9" maxlength="9" name="phone" value="<?php echo $phone ?>"></td>
				<td><label class="invalid"><?php echo $errorPhone; ?></label></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Prywatny adres e-mail</label></td>
				<td><input type="email" id="privateEmail" size="35" name="privateEmail" value="<?php echo $privateEmail ?>"></td>
				<td><label class="invalid"><?php echo $errorPrivateEmail; ?></label></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Adres w domenie aegee-gliwice.org</label></td>
				<td>
				<?php if ($aegeeEmail == 1) { ?>
					<input type='checkbox' name='aegeeEmail' value='aegeeEmail' checked/>
				<?php } else { ?>
					<input type='checkbox' name='aegeeEmail' value='aegeeEmail' />
				<?php }?>
				</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Data urodzenia</label></td>
				<td><input type="text" id="birthDate" size="10" maxlength="10" name="birthDate" value="<?php echo $birthDate ?>"></td>
				<td><label class="invalid"><?php echo $errorBirthDate; ?></label></td>
			</tr>
			<tr>	
				<td></td>
				<td><label>Numer karty członkowskiej</label></td>
				<td><input type="text" id="cardNumber" size="13" maxlength="13" name="cardNumber" value="<?php echo $cardNumber ?>"></td>
				<td><label class="invalid"><?php echo $errorCardNumber; ?></label></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Deklaracja</label></td>
				<td>
				<?php if ($declaration == 1) {?>
					<input type='checkbox' name='declaration' value='declaration' checked/>
				<?php } else { ?>
					<input type='checkbox' name='declaration' value='declaration' />
				<?php }?>
				</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Podłączenie do listy ogólnej</label></td>
				<td>
				<?php if ($connectedToList == 1) {?>
					<input type='checkbox' name='connectedToList' value='connectedToList' checked/>
				<?php } else {?>
					<input type='checkbox' name='connectedToList' value='connectedToList' />
				<?php }?>
				</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Mentor</label></td>
				<td><select name='mentor_id' id='mentor_id'>
				<?php
					$query = "SELECT id, firstName, lastName FROM `Members` WHERE mentor_id IN (0,-1)";
					$mentorResult = mysql_query($query);
					while ( $row = mysql_fetch_array($mentorResult) ) {
						if ($row["id"] == -1) {
							echo "<option value=\"". $row["id"]."\">".$row["firstName"]."</option>";
						} else if ($row["id"] == 0) {
							echo "<option value=\"". $row["id"]."\">".$row["firstName"]."</option>";
						} else {
							echo "<option value=\"". $row["id"]."\">".$row["firstName"]." ".$row["lastName"]."</option>";
						}
					}
				?>
				</select></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><label>Funkcja</label></td>
				<td><select name='type' id='type'>
					<option value="Z">Członek Zarządu</option>
					<option value="R">Członek Komisji Rewizyjnej</option>
					<option value="K">Koordynator</option>
					<option value="C">Członek zwyczajny</option>
					<option value="H">Członek honorowy</option>
				</select></td>
				<td></td>
			</tr>
			<tr>	
				<td colspan="3"></td>
				<td><input type="hidden" id="selectedId" name="selectedId" value="<?php echo $_POST["selectedId"]?>">
				<input name="submit_changes" value="Zapisz zmiany" class="blueButton" type="submit"/>
				</td>
			</tr>
		</table>
		</form>	
		<br>
	</div>
	<form id="details" method="post" action="selectedMember.php">
    	<input type="hidden" id="selectedId" name="selectedId" value="<?php echo $_POST["selectedId"]?>">
	</form>
	</div>
	<script>
		$( document ).ready(function() {
			$('select#mentor_id').val('<?php echo $mentor_id ?>');
			$('select#type').val('<?php echo $type ?>');
		});
		function goBack() {
			$("#details").submit();
		}
	</script>
	<?php 
	mysql_close($connection);
	?>
</body>
</html>