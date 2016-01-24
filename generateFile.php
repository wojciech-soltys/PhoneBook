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
				<li>
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
				<li class="active">
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
		<h3 class="colour blue">Struktura pliku</h3>
		<form class="" action="getFile.php" method="post" enctype="multipart/form-data">
		<table class="form">
			<col width="10%">
			<col width="3%">
			<col width="46%">
			<col width="41%">
			<tr>
				<td></td>
				<td><input type='checkbox' name='lp'/></td>
				<td><label>Lp.</label></td>				
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='checkbox' name='imie'/></td>
				<td><label>Imię</label></td>				
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='checkbox' name='nazwisko'/></td>
				<td><label>Nazwisko</label></td>				
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='checkbox' name='dataWst'/></td>
				<td><label>Data wstąpienia</label></td>				
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='checkbox' name='telefon'/></td>
				<td><label>Numer telefonu</label></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='checkbox' name='email'/></td>
				<td><label>Prywatny adres e-mail</label></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='checkbox' name='nrKarty'/></td>
				<td><label>Numer karty członkowskiej</label></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='checkbox' name='skladki'/></td>
				<td><label>Składki</label></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><input type='checkbox' name='oplacona'/></td>
				<td><label>Wyłącznie członkowie z opłaconą składką</label></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="3"></td>
				<td><input name="submit" value="Generuj raport" class="blueButton" type="submit"/></td>
			</tr>
		</table>
		</form>
	</div>
	</div>
	<?php 
	mysql_close($connection);
	?>
</body>
</html>