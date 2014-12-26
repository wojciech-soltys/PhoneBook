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
?>

<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="custom.css" rel="stylesheet" type="text/css">
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
           					<input name="submit" value="Dodaj członka" class="redButton" type="submit"/>
       					</p>
				</div>	
			</div>
		</div>
	</div>
	<div id="site-container">
	<form class="" action="createMember.php" method="post" enctype="multipart/form-data">
		<h3 class="colour blue">Dodaj członka</h3>
		<label>Imię: </label> 
		<input id="firstName" name="firstName" value="Imię członka" onfocus="if(this.value == 'Imię członka'){this.value = '';}" type="text" autocomplete="on" />
		<br>
		<label>Nazwisko: </label>
		<input id="lastName" name="lastName" value="Nazwisko członka" onfocus="if(this.value == 'Nazwisko członka'){this.value = '';}" type="text" autocomplete="on" />
		<br>
		<label>Data wstąpienia: </label>
		<input id="accesionDate" name="accesionDate" type="date" />
		<br>
		<label>Numer telefonu: </label>
		<input id="phoneNumber" name="phoneNumber" value="Numer telefonu" onfocus="if(this.value == 'Numer telefonu'){this.value = '';}" type="text" autocomplete="on" />
		<br>
		<label>Prywatny adres email: </label>
		<input id="email" name="email" value="Adres e-mail" onfocus="if(this.value == 'Numer telefonu'){this.value = '';}" type="text" autocomplete="on" />
		<br>
		<label>Adres w domenie aegee-gliwice.org: 
		</label><input type='checkbox' name='aegeeEmail' value='aegeeEmail'/>
		<br>
		<label>Data urodzenia: </label>
		<input id="birthDay" name="accesionDate" type="date" />
		<br>
		<label>Numer karty członkowskiej: </label>
		<input id="cardNumber" name="cardNumber" value="Numer karty członkowskiej" onfocus="if(this.value == 'Numer karty członkowskiej'){this.value = '';}" type="text" autocomplete="on" />
		<br>
		<label>Deklaracja: </label>
		<input type='checkbox' name='declaration' value='declaration' />
		<br>
		<label>Podłączenie do listy ogólnej: </label>
		<input type='checkbox' name='connectedToList' value='connectedToList' />
		<br>
		<select name='mentorID' id='mentorID'>
			<?php 
				$query = "SELECT id,firstName, lastName FROM `Members` WHERE mentorID IN (0,-1)";
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
		<input name="submit" value="Dodaj członka" class="redButton" type="submit"/>
	</form>
	</div>
	</div>
</body>
</html>