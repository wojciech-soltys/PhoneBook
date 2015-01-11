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

$error = ''; // Variable To Store Error Message
$firstName = '';
if (isset ( $_POST ['submit'] )) {
	$firstName = $_POST ['firstName'];
	$lastName = $_POST ['lastName'];
	$accesionDate = $_POST ['accesionDate'];
	$phoneNumber = $_POST ['phoneNumber'];
	$email = $_POST ['email'];
	$aegeeEmail = $_POST['aegeeEmail'];
	$birthDay = $_POST ['birthDay'];
	$cardNumber = $_POST ['cardNumber'];
	$declaration = $_POST['declaration'];
	$connectedToList = $_POST['connectedToList'];
	$mentor_id = $_POST['mentor_id'];
	$memberType = $_POST['memberType'];
	$login = $_POST['login'];
	$password1 = '';
	$password1 = $_POST['password1'];
	$password2 = $_POST['password2'];
	
	
	if (empty ( $firstName )) {
		$error = 'Puste Imię członka';
	} else if (strlen( $firstName ) > 255 ) {
		$error = 'Zbyt długie Imię';
	}
	
	if (empty ( $lastName )) {
		$error = 'Puste Nazwisko członka';
	} else if (strlen( $firstName ) > 255 ) {
		$error = 'Zbyt długie Nazwisko';
	}
	
	if (empty ( $accesionDate )) {
		$error = 'Pusta Data wstąpienia';
	} else if (strlen( $accesionDate ) != 10 ) {
		$error = 'Błędna długość Daty wstąpienia';
	}
	
	if (empty ( $phoneNumber )) {
		$error = 'Pusty numer telefonu';
	} else if (strlen( $phoneNumber ) != 9 ) {
		$error = 'Błędna długość numeru telefonu';
	}
	
	if (empty ( $email )) {
		$error = 'Pusty adres e-mail';
	} else if (strlen( $email ) > 255 ) {
		$error = 'Błędna długość adresu e-mail';
	}
	
	if (isset( $aegeeEmail )) {
		$aegeeEmail = '1';
	} else {
		$aegeeEmail ='0';
	}
	
	if (empty ( $birthDay )) {
		$error = 'Pusta data urodzenia';
	} else if (strlen( $birthDay ) > 10 ) {
		$error = 'Błędna długość daty uroedzenia';
	}
	
	if (strlen( $cardNumber ) > 13 ) {
		$error = 'Błędna długość numeru karty członkowskiej';
	}
	
	if (isset( $declaration )) {
		$declaration = '1';
	} else {
		$declaration ='0';
	}
	
	if (isset( $connectedToList )) {
		$connectedToList = '1';
	} else {
		$connectedToList ='0';
	}
	
	if (empty ( $login )) {
		$error = 'Pusty login';
	} else if (strlen( $login ) > 255 ) {
		$error = 'Błędna długość loginu';
	}
	
	if (empty ( $password1 )) {
		$error = 'Puste hasło';
	} else if (strlen( $password1 ) > 32 ) {
		$error = 'Błędna długość hasła';
	}
	
	if (empty ( $password2 )) {
		$error = 'Puste powtórzone hasło';
	} else if (strlen( $password2 ) > 32 ) {
		$error = 'Błędna długość powtórzonego hasła';
	}
	
	if($password1 !== $password2) {
		$error = 'Wpisane hasła nie są identyczne';
	} else {
		$password = hash('sha256', $password1);
	}
	
	if(strlen($error) == 0) {
		$query = "INSERT INTO Members (firstName, lastName, accessionDate, phone, privateEmail, 
				aegeeEmail, birthDate, cardNumber, declaration, connectedToList, mentor_id, type) 
		VALUES(". $firstName.",".$lastName.",".$accesionDate.",".$phoneNumber.",".$email.","
				.$aegeeEmail.",".$birthDay.",".$cardNumber.",".$declaration.",".$connectedToList.",".$mentor_id.",".$memberType.")";
		$result = mysql_query($query);
		if($result === TRUE) {
			$userdId = mysql_insert_id();
			$query = "INSERT INTO Login ( username, password, member_id) VALUES
				(".$login.",".$password.",".$userdId.")";
			$result = mysql_query($query);
			if($result === TRUE) {
				$error = "Poprawnie dodano członka";
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
		<br>
		<label>Nazwisko: </label>
		<input id="lastName" name="lastName" placeholder="Nazwisko członka" type="text" autocomplete="on" maxlength="255"/>
		<br>
		<label>Data wstąpienia: </label>
		<input id="accesionDate" name="accesionDate" placeholder="RRRR-MM-DD" type="text"autocomplete="on" maxlength="10"/>
		<br>
		<label>Numer telefonu: </label>
		<input id="phoneNumber" name="phoneNumber" placeholder="123456789" type="text" autocomplete="on" maxlength="9" />
		<br>
		<label>Prywatny adres email: </label>
		<input id="email" name="email" placeholder="abc@gmail.com" type="text" autocomplete="on" maxlength="255"/>
		<br>
		<label>Adres w domenie aegee-gliwice.org: 
		</label><input type='checkbox' name='aegeeEmail'/>
		<br>
		<label>Data urodzenia: </label>
		<input id="birthDay" name="birthDay" placeholder="RRRR-MM-DD" type="text" autocomplete="on" maxlength="10" />
		<br>
		<label>Numer karty członkowskiej: </label>
		<input id="cardNumber" name="cardNumber" placeholder="123456-123456" type="text" maxlength="13"/>
		<br>
		<label>Deklaracja: </label>
		<input type='checkbox' name='declaration'/>
		<br>
		<label>Podłączenie do listy ogólnej: </label>
		<input type='checkbox' name='connectedToList'/>
		<br>
		<label>Mentor: </label>
		<select name='mentor_id' id='mentor_id'>
			<?php 
				$query = "SELECT id, firstName, lastName FROM `Members` WHERE mentor_id IN (0,-1)";
				$mentorResult = mysql_query($query);
				while ( $row = mysql_fetch_array($mentorResult) ) {
					if ($row["id"] == -1) {
						echo "<option selected value=\"". $row["id"]."\">".$row["firstName"]."</option>";
					} else if ($row["id"] == 0) {
						echo "<option value=\"". $row["id"]."\">".$row["firstName"]."</option>";
					} else {
						echo "<option value=\"". $row["id"]."\">".$row["firstName"]." ".$row["lastName"]."</option>";
					}
				}
			?>
		</select>
		<br>
		<label>Typ członka: </label>
		<select name='memberType' id='memberType'>
			<option selected value="C">Członek zwyczajny</option>
			<option value="K">Koordynator</option>
			<option value="Z">Członek zarządu</option>
			<option value="R">Komisja rewizyjna</option>
			<option value="H">Członek honorowy</option>
		</select>
		<br>
		<p><?php echo $error; ?></p>
		<br>
		<label>Login:</label>
		<input id="login" name="login" placeholder="login" type="text" autocomplete="off" maxlength="255"/>
		<br>
		<label>Hasło</label>
		<input id="password1" name="password1" placeholder="Hasło" type="password" autocomplete="off" minlenght="6" maxlenght="32"/>
		<input id="password2" name="password2" placeholder="Powtórz hasło" type="password" autocomplete="off" minlenght="6" maxlenght="32"/>
		<input name="submit" value="Dodaj członka" class="red" type="submit"/>
	</form>
	</div>
	</div>
</body>
</html>