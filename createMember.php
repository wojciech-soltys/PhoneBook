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


$firstName = '';
$lastName = '';
$accessionDate = '';
$phone = '';
$privateEmail = '';
$aegeeEmail = 0;
$birthDate = '';
$cardNumber = '';
$declaration = 0;
$connectedToList = 0;
$mentor_id = -1;
$type = 'C';
$login = '';
$password1 = '';
$password2 = '';

//Variables To Store Error Message
$error = ''; 
$errorFirstName = '';
$errorLastName = '';
$errorAccessionDate = '';
$errorPhone = '';
$errorPrivateEmail = '';
$errorBirthDate = '';
$errorCardNumber = '';
$errorLogin = '';
$errorPassword1 = '';
$errorPassword2 = '';
if (isset ( $_POST ['submit'] )) {
	
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
	
	$aegeeEmail = isset($_POST['aegeeEmail']) ? '1' : '0';
	
	$birthDate = $_POST ['birthDate'];
	$birthDate = stripslashes($birthDate);
	$birthDate = mysql_real_escape_string($birthDate);
	
	$cardNumber = $_POST ['cardNumber'];
	$cardNumber = stripslashes($cardNumber);
	$cardNumber = mysql_real_escape_string($cardNumber);
	
	$declaration = isset($_POST['declaration']) ? '1' : '0';
	
	$connectedToList = isset($_POST['connectedToList']) ? '1' : '0';
	
	$mentor_id = $_POST['mentor_id'];
	
	$type = $_POST['type'];
	
	/*$login = $_POST['login'];
	$login = stripslashes($login);
	$login = mysql_real_escape_string($login);
	
	$password1 = $_POST['password1'];
	$password1 = stripslashes($password1);
	$password1 = mysql_real_escape_string($password1);
	
	$password2 = $_POST['password2'];
	$password2 = stripslashes($password2);
	$password2 = mysql_real_escape_string($password2);*/
	
	
	$errorFirstName = ((empty($firstName)) ? 'Pole imię jest puste. ' : '');
	$errorFirstName = ((strlen($firstName) > 70) ? 'Zbyt duża liczba znaków w polu imię.' : '');
	$errorLastName = ((empty($lastName)) ? 'Pole nazwisko jest puste. ' : '');
	$errorLastName = ((strlen($lastName) > 70) ? 'Zbyt duża liczba znaków w polu nazwisko.' : '');
	if (empty($accessionDate)) {
		$accessionDate = '1900-01-01';
	} else {
		$errorAccessionDate = ((preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $accessionDate) == 0) ? 'Niepoprawna wartość w polu data wstąpienia. Wpisz wartość według schematu: RRRR-MM-DD.' : '');
	}
	if (empty($phone)) { 
		$phone = '-';
	} else {
		$errorPhone = ((preg_match("/^[0-9]{9}$/", $phone) == 0) ? 'Niepoprawny numer telefonu.' : ''); 
	}
	$errorPrivateEmail = ((!filter_var($privateEmail, FILTER_VALIDATE_EMAIL)) ? 'Niepoprawny adres poczty elektronicznej.' : '');
	if (empty($birthDate)) {
		$birthDate = '1900-01-01';
	} else {
		$errorBirthDate = ((preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $birthDate) == 0) ? 'Niepoprawna wartość w polu data urodzenia. Wpisz wartość według schematu: RRRR-MM-DD.' : '');
	}
	if (empty($cardNumber)) {
		$cardNumber = '-';
	} else {
		$errorCardNumber = ((preg_match("/^[A-Z0-9]{6}-[A-Z0-9]{6}$/", $cardNumber) == 0) ? 'Niepoprawna wartość w polu numer karty członkowskiej. Wpisz wartość według schematu: xxxxxx-xxxxxx.' : '');
	}	
	/*if (empty ( $login )) {
		$errorLogin = 'Pusty login';
	} else if (strlen( $login ) > 255 ) {
		$errorLogin = 'Za długi login (maksymalnie 255 znaków)';
	}
	
	if (empty ( $password1 )) {
		$errorPassword1 = 'Puste hasło';
	} else if (strlen( $password1 ) > 32 ) {
		$errorPassword1 = 'Za długie hasło (maksymalnie 32 znaki)';
	}
	
	if (empty ( $password2 )) {
		$errorPassword2 = 'Puste powtórzone hasło';
	} else if (strlen( $password2 ) > 32 ) {
		$error = 'Za długie powtórzone hasło (maksymalnie 32 znaki)';
	}
	
	if((strlen($errorPassword1) == 0) && (strlen($errorPassword2) == 0)) {
		if($password1 !== $password2) {
			$errorPassword1 = 'Wpisane hasła nie są identyczne';
			$errorPassword2 = 'Wpisane hasła nie są identyczne';
		} else {
			$password = hash('sha256', $password1);
		}
	}*/

	
	if ((strlen($errorFirstName) == 0) && (strlen($errorLastName) == 0) && (strlen($errorAccessionDate) == 0) && (strlen($errorPhone) == 0) 
			&& (strlen($errorPrivateEmail) == 0) && (strlen($errorBirthDate) == 0) && (strlen($errorCardNumber) == 0) &&
			(strlen($errorLogin) == 0) && (strlen($errorPassword1) == 0) && (strlen($errorPassword2) == 0)) {
		$query = "INSERT INTO `Members` (firstName, lastName, accessionDate, phone, privateEmail, 
				aegeeEmail, birthDate, cardNumber, declaration, connectedToList, mentor_id, type) 
		VALUES('$firstName','$lastName','$accessionDate','$phone','$privateEmail',
				$aegeeEmail,'$birthDate','$cardNumber',$declaration,'$connectedToList','$mentor_id','$type')";
		$result = mysql_query($query);
 		if (!$result) {
    		die('Błąd podczas zapisu danych ' . mysql_error());
		} else {
			$userdId = mysql_insert_id();
			/*$query = "INSERT INTO `Login` ( username, password, member_id) VALUES
				('$login','$password',$userdId)";
			$result = mysql_query($query);*/
			if(!$result) {
				die('Błąd podczas zapisu danych logowania. Skontaktuj sie z administratorem strony.' . mysql_error());
			} else {
				$error = 'Poprawnie dodano członka.';
			}
		} 
	}
}
?>

<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
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
       				</p>
				</div>	
			</div>
		</div>
	</div>
	<div id="site-container">
	<form class="" action="createMember.php" method="post" enctype="multipart/form-data">
		<h3 class="colour blue">Dodaj członka</h3>
		
		<label>Imię: </label> 
		<input id="firstName" name="firstName" placeholder="Imię członka" type="text" autocomplete="on" maxlength="255" value="<?php echo $firstName; ?>"/>
		<label class="invalid"><?php echo $errorFirstName; ?></label>
		<br>
		
		<label>Nazwisko: </label>
		<input id="lastName" name="lastName" placeholder="Nazwisko członka" type="text" autocomplete="on" maxlength="255" value="<?php echo $lastName;?>"/>
		<label class="invalid"><?php echo $errorLastName; ?></label>
		<br>
		
		<label>Data wstąpienia: </label>
		<input id="accessionDate" name="accessionDate" placeholder="RRRR-MM-DD" type="text"autocomplete="on" maxlength="10" value="<?php echo $accessionDate;?>"/>
		<label class="invalid"><?php echo $errorAccessionDate; ?></label>
		<br>
		
		<label>Numer telefonu: </label>
		<input id="phone" name="phone" placeholder="123456789" type="text" autocomplete="on" maxlength="9" value="<?php echo $phone;?>"/>
		<label class="invalid"><?php echo $errorPhone; ?></label>
		<br>
		
		<label>Prywatny adres email: </label>
		<input id="privateEmail" name="privateEmail" placeholder="abc@gmail.com" type="text" autocomplete="on" maxlength="255"  value="<?php echo $privateEmail;?>"/>
		<label class="invalid"><?php echo $errorPrivateEmail; ?></label>
		<br>
		<label>Adres w domenie aegee-gliwice.org: </label>
			<?php if ($aegeeEmail == 1) { ?>
				<input type='checkbox' name='aegeeEmail' checked/>
			<?php } else { ?>
				<input type='checkbox' name='aegeeEmail'/>
			<?php }?>
		<br>

		<label>Data urodzenia: </label>
		<input id="birthDate" name="birthDate" placeholder="RRRR-MM-DD" type="text" autocomplete="on" maxlength="10" value="<?php echo $birthDate;?>"/>
		<label class="invalid"><?php echo $errorBirthDate; ?></label>
		<br>
		
		<label>Numer karty członkowskiej: </label>
		<input id="cardNumber" name="cardNumber" placeholder="123456-123456" type="text" maxlength="13" value="<?php echo $cardNumber;?>"/>
		<label class="invalid"><?php echo $errorCardNumber; ?></label>
		<br>
		
		<label>Deklaracja: </label>
			<?php if ($declaration == 1) { ?>
				<input type='checkbox' name='declaration' chcecked/>
			<?php } else { ?>
				<input type='checkbox' name='declaration'/>
			<?php }?>
		<br>
		
		<label>Podłączenie do listy ogólnej: </label>
			<?php if ($connectedToList == 1) { ?>
				<input type='checkbox' name='declaration' chcecked/>
			<?php } else { ?>
				<input type='checkbox' name='connectedToList'/>
			<?php }?>
		<br>
		
		<label>Mentor: </label>
		<select name='mentor_id' id='mentor_id'>
			<?php 
				$query = "SELECT id, firstName, lastName FROM `Members` WHERE mentor_id IN (0,-1)";
				$mentorResult = mysql_query($query);
				while ( $row = mysql_fetch_array($mentorResult) ) {
					if ($row["id"] == -1) {
						if($mentor_id == $row["id"]) {
							echo "<option selected value=\"". $row["id"]."\">".$row["firstName"]."</option>";
						} else {
							echo "<option value=\"". $row["id"]."\">".$row["firstName"]."</option>";
						}	
					} else if ($row["id"] == 0) {
						if($mentor_id == $row["id"]) {
							echo "<option selected value=\"". $row["id"]."\">".$row["firstName"]."</option>";
						} else {
							echo "<option value=\"". $row["id"]."\">".$row["firstName"]."</option>";
						}
					} else {
						if($mentor_id == $row["id"]) {
							echo "<option selected value=\"". $row["id"]."\">".$row["firstName"]." ".$row["lastName"]."</option>";
						} else {
							echo "<option value=\"". $row["id"]."\">".$row["firstName"]." ".$row["lastName"]."</option>";
						}
					}
				}
			?>
		</select>
		<br>
		
		<label>Typ członka: </label>
		<select name='type' id='type'>
			<?php if ($type == 'C') { ?>
				<option selected value="C">Członek zwyczajny</option>
			<?php } else { ?>
				<option value="C">Członek zwyczajny</option>
			<?php }?>
			<?php if ($type == 'K') { ?>
				<option selected value="K">Koordynator</option>
			<?php } else { ?>
				<option value="K">Koordynator</option>
			<?php }?>
			<?php if ($type == 'Z') { ?>
				<option selected value="Z">Członek zarządu</option>
			<?php } else { ?>
				<option value="Z">Członek zarządu</option>
			<?php }?>
			<?php if ($type == 'R') { ?>
				<option selected value="R">Komisja rewizyjna</option>
			<?php } else { ?>
				<option value="R">Komisja rewizyjna</option>
			<?php }?>
			<?php if ($type == 'H') { ?>
				<option selected value="H">Członek honorowy</option>
			<?php } else { ?>
				<option value="H">Członek honorowy</option>
			<?php }?>
		</select>
		<br>
		<br>
		
		<!--  <label>Login:</label>
		<input id="login" name="login" placeholder="login" type="text" autocomplete="off" maxlength="255" value="<?php echo $login;?>"/>
		<label class="invalid"><?php echo $errorLogin; ?></label>
		<br>
		
		<label>Hasło</label>
		<input id="password1" name="password1" placeholder="Hasło" type="password" autocomplete="off" minlenght="6" maxlenght="32" value="<?php echo $password1;?>"/>
		<label class="invalid"><?php echo $errorPassword1; ?></label>
		<br>
		<label>Potwierdź Hasło</label>
		<input id="password2" name="password2" placeholder="Powtórz hasło" type="password" autocomplete="off" minlenght="6" maxlenght="32" value="<?php echo $password2;?>"/>
		<label class="invalid"><?php echo $errorPassword2; ?></label>
		<br>-->
		<input name="submit" value="Dodaj członka" class="red" type="submit"/>
		<p <?php echo $error; ?></p>
	</form>
	</div>
	</div>
</body>
</html>