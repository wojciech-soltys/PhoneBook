
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
<body home page page-id-131 page-template-default rf_wrapper>
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
$mentorID = $row["mentorID"];

if (isset($_POST ['submit_changes'])) {
	$firstName = $_POST ['firstName'];
	$firstName = stripslashes($firstName);
	$firstName = mysql_real_escape_string($firstName);
	
	$lastName = $_POST ['lastName'];
	$lastName = stripslashes($lastName);
	$lastName = mysql_real_escape_string($lastName);
	
	$accessionDate = $_POST ['accessionDate'];
	$accessionDate = stripslashes($accessionDate);
	$accessionDate = mysql_real_escape_string($accessionDate);
	
	$phone = $_POST ['phone'];
	$phone = stripslashes($phone);
	$phone = mysql_real_escape_string($phone);
	
	$privateEmail = $_POST ['privateEmail'];
	$privateEmail = stripslashes($privateEmail);
	$privateEmail = mysql_real_escape_string($privateEmail);
	
	if (isset($_POST ['aegeeEmail'])) {
		$aegeeEmail = 1;
	} else {
		$aegeeEmail = 0;
	}
	
	$birthDate = $_POST ['birthDate'];
	$birthDate = stripslashes($birthDate);
	$birthDate = mysql_real_escape_string($birthDate);
	
	$cardNumber = $_POST ['cardNumber'];
	$cardNumber = stripslashes($cardNumber);
	$cardNumber = mysql_real_escape_string($cardNumber);
	
	if (isset($_POST ['declaration'])) {
		$declaration = 1;
	} else {
		$declaration = 0;
	}
	
	if (isset($_POST ['connectedToList'])) {
		$connectedToList = 1;
	} else {
		$connectedToList = 0;
	}
	
	$mentorID = $_POST ['mentorID'];
	
	$errorFirstName = ((empty($firstName)) ? 'Pole imię jest puste. ' : '');
	$errorFirstName = ((strlen($firstName) > 70) ? 'Zbyt duża liczba znaków w polu imię.' : '');
	$errorLastName = ((empty($lastName)) ? 'Pole nazwisko jest puste. ' : '');
	$errorLastName = ((strlen($lastName) > 70) ? 'Zbyt duża liczba znaków w polu nazwisko.' : '');
	$errorAccessionDate = ((preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $accessionDate) == 0) ? 'Niepoprawna wartość w polu data wstąpienia. Wpisz wartość według schematu: RRRR-MM-DD.' : '');
	$errorPhone = ((preg_match("/^[0-9]{9}$/", $phone) == 0) ? 'Niepoprawny numer telefonu.' : ''); 
	$errorPrivateEmail = ((!filter_var($privateEmail, FILTER_VALIDATE_EMAIL)) ? 'Niepoprawny adres poczty elektronicznej.' : '');
	$errorBirthDate = ((preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $birthDate) == 0) ? 'Niepoprawna wartość w polu data urodzenia. Wpisz wartość według schematu: RRRR-MM-DD.' : '');
	$errorCardNumber = ((preg_match("/^[A-Z0-9]{6}-[A-Z0-9]{6}$/", $cardNumber) == 0) ? 'Niepoprawna wartość w polu numer karty członkowskiej. Wpisz wartość według schematu: xxxxxx-xxxxxx.' : ''); 
	if ((strlen($errorFirstName) == 0) && (strlen($errorLastName) == 0) && (strlen($errorAccessionDate) == 0) && (strlen($errorPhone) == 0) && (strlen($errorPrivateEmail) == 0) && (strlen($errorBirthDate) == 0) && (strlen($errorCardNumber) == 0)) {
		$query = "UPDATE `Members` SET firstName = '$firstName', lastName = '$lastName', accessionDate = '$accessionDate', phone = '$phone', privateEmail = '$privateEmail', aegeeEmail = $aegeeEmail," .
		"birthDate = '$birthDate', cardNumber = '$cardNumber', declaration = $declaration, connectedToList = $connectedToList, mentorID = $mentorID  WHERE id=".$_POST["selectedId"];
		$retval = mysql_query($query, $connection);
		if(! $retval )
		{
			die('Błąd podczas zapisu danych: ' . mysql_error());
		}
		echo "<script type='text/javascript'>alert('Dane zostały zapisane');</script>";
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
           					<input name="createMember" onclick="window.location.href='createMember.php';" value="Dodaj członka" class="redButton" type="button"/>
       					</p>
				</div>	
			</div>
		</div>
	</div>
	<div id="site-container">
		<?php 
			
		?>
		<h3 class="colour blue">Zmiana danych osobowych członka <?php echo $row["firstName"] . " " . $row["lastName"]  ?> </h3>
		<form action="selectedMemberEdit.php" id="member" method="post" enctype="multipart/form-data">
		<label>Id: </label><?php echo $id ?><br>
		<div>
			<label>Imię: </label><input type="text" id="firstName" name="firstName" maxlenght="70" value="<?php echo $firstName ?>"> <label class="invalid"><?php echo $errorFirstName; ?></label>
		</div>
		<div>
			<label>Nazwisko: </label><input type="text" id="lastName" name="lastName" maxlenght="70" value="<?php echo $lastName ?>"> <label class="invalid"><?php echo $errorLastName; ?></label>
		</div>
		<div>
			<label>Data wstąpienia: </label><input type="text" id="accessionDate" size="7" maxlength="10" name="accessionDate" value="<?php echo $accessionDate ?>"> <label class="invalid"><?php echo $errorAccessionDate; ?></label>
		</div>
		<div>
			<label>Numer telefonu: </label><input type="text" id="phone" size="7" maxlength="9" name="phone" value="<?php echo $phone ?>"> <label class="invalid"><?php echo $errorPhone; ?></label>
		</div>
		<div>
			<label>Prywatny adres email: </label><input type="email" id="privateEmail" size="35" name="privateEmail" value="<?php echo $privateEmail ?>"> <label class="invalid"><?php echo $errorPrivateEmail; ?></label>
		</div>
		<div>
			<?php if ($aegeeEmail == 1) { ?>
				<label>Adres w domenie aegee-gliwice.org: </label><input type='checkbox' name='aegeeEmail' value='aegeeEmail' checked/>
			<?php } else { ?>
				<label>Adres w domenie aegee-gliwice.org: </label><input type='checkbox' name='aegeeEmail' value='aegeeEmail' />
			<?php }?>
		</div>
		<div>
			<label>Data urodzenia: </label><input type="text" id="birthDate" size="7" maxlength="10" name="birthDate" value="<?php echo $birthDate ?>"> <label class="invalid"><?php echo $errorBirthDate; ?></label>
		</div>
		<div>
			<label>Numer karty członkowskiej: </label><input type="text" id="cardNumber" size="11" maxlength="13" name="cardNumber" value="<?php echo $cardNumber ?>"> <label class="invalid"><?php echo $errorCardNumber; ?></label>
		</div>
		<div>
			<?php if ($declaration == 1) {?>
				<label>Deklaracja: </label><input type='checkbox' name='declaration' value='declaration' checked/><br>
			<?php } else { ?>
				<label>Deklaracja: </label><input type='checkbox' name='declaration' value='declaration' /><br>
			<?php }?>
		</div>
		<div>
			<?php if ($connectedToList == 1) {?>
				<label>Podłączenie do listy ogólnej: </label><input type='checkbox' name='connectedToList' value='connectedToList' checked/><br>
			<?php } else {?>
				<label>Podłączenie do listy ogólnej: </label><input type='checkbox' name='connectedToList' value='connectedToList' /><br>
			<?php }?>
		</div>
		<div>
			<label>Mentor: </label>
			<select name='mentorID' id='mentorID'>
			<?php
				$query = "SELECT id, firstName, lastName FROM `Members` WHERE mentorID IN (0,-1)";
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
			</select>
		</div>
		<input type="hidden" id="selectedId" name="selectedId" value="<?php echo $_POST["selectedId"]?>">
		<br><br><input name="submit_changes" value="Zapisz zmiany" class="red" type="submit"/>
		</form>	
		<br>
	</div>
	<form id="details" method="post" action="selectedMember.php">
    	<input type="hidden" id="selectedId" name="selectedId" value="<?php echo $_POST["selectedId"]?>">
	</form>
	</div>
	<script>
		$( document ).ready(function() {
			$('select').val('<?php echo $mentorID ?>');
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